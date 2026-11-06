<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/** AI calling agents (manager/ai-agents): agents, results to follow up, call log, provider settings, provider webhook. */
class Ci_admin_ai_agents extends CI_Controller
{
	private $uid = 0;
	private $role = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->model(array('ci_admin_model', 'comman_model', 'dialer_campaign_model', 'ai_agent_model'));
		$this->dialer_campaign_model->ensure_schema();
		$this->ai_agent_model->ensure_schema();
		$method = $this->router->fetch_method();
		// The provider and cron call these without a CRM login; they need the AI webhook key.
		if (in_array($method, array('webhook', 'cron'), true)) {
			$want = $this->ai_agent_model->settings()['webhook_key'];
			$got = isset($_SERVER['HTTP_X_WEBHOOK_KEY']) ? (string)$_SERVER['HTTP_X_WEBHOOK_KEY'] : (string)$this->input->get('key');
			if ($want === '' || $got === '' || !hash_equals($want, $got)) {
				http_response_code(403);
				$this->_json(array('status' => false, 'message' => 'Invalid key'));
				exit;
			}
			return;
		}
		header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->uid = (int)$this->session->userdata('manager_id');
		$this->role = (int)$this->session->userdata('manager_role');
		if ($this->uid <= 0) {
			if ($this->input->is_ajax_request()) { $this->_json(array('status' => false, 'message' => 'Please sign in again')); exit; }
			redirect('/manager/login');
			exit;
		}
		if (!crm_can('ai_agents', 'read')) {
			if ($this->input->is_ajax_request()) { $this->_json(array('status' => false, 'message' => 'No access to AI agents')); exit; }
			$this->session->set_flashdata('error_message', 'You do not have access to AI Calling Agents.');
			redirect(crm_landing_url());
			exit;
		}
	}

	private function _json($a)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($a);
	}

	private function _can_write() { return crm_can('ai_agents', 'write'); }
	private function _can_settings() { return crm_can('ai_agents', 'write') && crm_can('integrations', 'write'); }

	public function index()
	{
		$A = $this->ai_agent_model;
		$scope = $A->scope_sql($this->uid, $this->role);
		$hot = "'" . implode("','", Ai_agent_model::$hot) . "'";
		$s = $A->settings();
		$data = array(
			'info' => array('title' => 'AI Calling Agents', 'page_heading' => 'AI Agents'),
			'agents' => $A->agents(),
			'sum' => $A->summary($scope),
			'inbox' => $A->calls("Q.is_test = 0 AND Q.outcome IN ({$hot}) AND Q.handled_by = 0 AND {$scope}", 200),
			'log' => $A->calls("Q.status != 'Queued' AND {$scope}", 200),
			'not_ready' => $A->not_ready_reason($s),
			'provider' => Ai_agent_model::$providers[$s['provider']],
			'can_write' => $this->_can_write(),
			'can_settings' => $this->_can_settings(),
			'languages' => Ai_agent_model::$languages,
			'goals' => Ai_agent_model::$goals,
			'outcomes' => Ai_agent_model::$outcomes
		);
		$this->load->view('manager/ai_agents/index', $data);
	}

	public function form($id = 0)
	{
		$id = (int)$id;
		if (!$this->_can_write()) {
			$this->session->set_flashdata('error_message', 'You do not have Full access to AI agents.');
			redirect('manager/ai-agents');
			return;
		}
		$agent = $id ? $this->ai_agent_model->agent($id) : null;
		if ($id && empty($agent)) { redirect('manager/ai-agents'); return; }
		$copy = (int)$this->input->get('copy');
		if (!$id && $copy && ($src = $this->ai_agent_model->agent($copy))) {
			$agent = $src;
			$agent['id'] = 0;
			$agent['name'] = mb_substr('Copy of ' . $src['name'], 0, 100);
			$agent['status'] = 'Draft';
		}
		$s = $this->ai_agent_model->settings();
		$this->load->view('manager/ai_agents/form', array(
			'info' => array('title' => $id ? 'Edit AI agent' : 'New AI agent', 'page_heading' => 'AI Agents'),
			'agent' => $agent,
			'languages' => Ai_agent_model::$languages,
			'voices' => Ai_agent_model::$voices,
			'goals' => Ai_agent_model::$goals,
			'not_ready' => $this->ai_agent_model->not_ready_reason($s),
			'provider' => $s['provider'],
			'company' => $s['company'],
			'timezone' => $s['timezone']
		));
	}

	private function _fields()
	{
		$in = $this->input;
		$t = function ($k, $max) use ($in) { return mb_substr(trim(str_replace("\r\n", "\n", (string)$in->post($k, true))), 0, $max); };
		$name = $t('name', 100);
		if ($name === '') return array(null, 'Give the agent a name.');
		$time = function ($v, $def) { return preg_match('~^([01]\d|2[0-3]):[0-5]\d$~', (string)$v) ? $v : $def; };
		$from = $time($in->post('call_from'), '09:00');
		$to = $time($in->post('call_to'), '18:00');
		if ($to <= $from) return array(null, 'Calling hours: the end time must be after the start time.');
		$days = $in->post('call_days');
		$days = is_array($days) ? array_values(array_intersect(array_map('intval', $days), range(1, 7))) : array();
		if (!$days) return array(null, 'Pick at least one calling day.');
		$transfer = preg_replace('~[^\d+]~', '', (string)$in->post('transfer_number'));
		if ($transfer !== '' && strlen(preg_replace('/\D/', '', $transfer)) < 7) return array(null, 'The transfer number looks too short.');
		$status = (string)$in->post('status');
		$language = (string)$in->post('language');
		$voice = (string)$in->post('voice');
		$goal = (string)$in->post('goal');
		return array(array(
			'name' => $name,
			'status' => in_array($status, array('Draft', 'Active', 'Paused'), true) ? $status : 'Draft',
			'language' => isset(Ai_agent_model::$languages[$language]) ? $language : 'en',
			'voice' => isset(Ai_agent_model::$voices[$voice]) ? $voice : 'female',
			'voice_id' => preg_replace('~[^\w.\-]~', '', $t('voice_id', 100)),
			'disclose_ai' => $in->post('disclose_ai') ? 1 : 0,
			'goal' => isset(Ai_agent_model::$goals[$goal]) ? $goal : 'qualify',
			'pitch' => $t('pitch', 4000),
			'opening' => $t('opening', 500),
			'questions' => $t('questions', 3000),
			'objections' => $t('objections', 4000),
			'closing' => $t('closing', 1000),
			'transfer_number' => substr($transfer, 0, 30),
			'dnd_on_request' => $in->post('dnd_on_request') ? 1 : 0,
			'max_minutes' => max(1, min(15, (int)$in->post('max_minutes'))),
			'call_from' => $from,
			'call_to' => $to,
			'call_days' => implode(',', $days),
			'daily_limit' => max(1, min(5000, (int)$in->post('daily_limit')))
		), '');
	}

	public function ajax_save()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to AI agents.'));
		$id = (int)$this->input->post('id');
		if ($id && !$this->ai_agent_model->agent($id)) return $this->_json(array('status' => false, 'message' => 'Agent not found.'));
		list($data, $err) = $this->_fields();
		if ($err !== '') return $this->_json(array('status' => false, 'message' => $err));
		if (!$id) $data['created_by'] = $this->uid;
		$saved = $this->ai_agent_model->save_agent($data, $id);
		$this->ci_admin_model->log_admin_action($id ? 'ai_agent_update' : 'ai_agent_create', 'AI agent #' . $saved . ' ' . $data['name'], 'SUCCESS');
		$this->_json(array('status' => true, 'id' => $saved));
	}

	/** Prompt preview for the form (uses the unsaved values). */
	public function ajax_preview()
	{
		list($data, $err) = $this->_fields();
		if (!$data) $data = array('name' => 'Agent', 'disclose_ai' => 1, 'language' => 'en', 'goal' => 'qualify', 'pitch' => '', 'questions' => '', 'objections' => '', 'closing' => '', 'opening' => '', 'transfer_number' => '', 'max_minutes' => 5);
		$sample = array('contact' => 'Ahmed', 'customer' => 'Al Noor Trading LLC');
		$this->_json(array('status' => true, 'first' => $this->ai_agent_model->first_message($data, $sample), 'prompt' => $this->ai_agent_model->build_prompt($data, $sample)));
	}

	public function ajax_status()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to AI agents.'));
		$a = $this->ai_agent_model->agent((int)$this->input->post('id'));
		$st = (string)$this->input->post('status');
		if (!$a || !in_array($st, array('Active', 'Paused'), true)) return $this->_json(array('status' => false, 'message' => 'Not changed'));
		$this->ai_agent_model->save_agent(array('status' => $st), (int)$a['id']);
		$this->ci_admin_model->log_admin_action('ai_agent_status', 'AI agent #' . (int)$a['id'] . ' ' . $st, 'SUCCESS');
		$this->_json(array('status' => true));
	}

	public function ajax_delete()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to AI agents.'));
		$a = $this->ai_agent_model->agent((int)$this->input->post('id'));
		if (!$a) return $this->_json(array('status' => false, 'message' => 'Agent not found'));
		$calls = (int)$this->db->where('agent_id', (int)$a['id'])->where('is_test', 0)->where('status !=', 'Cancelled')->count_all_results('ci_ai_calls');
		if ($calls > 0) return $this->_json(array('status' => false, 'message' => 'This agent has made calls. Pause it instead.'));
		$this->db->where('agent_id', (int)$a['id'])->delete('ci_ai_calls');
		$this->db->where('id', (int)$a['id'])->delete('ci_ai_agents');
		$this->db->where('ai_agent_id', (int)$a['id'])->update('ci_dialer_campaigns', array('ai_agent_id' => 0));
		$this->ci_admin_model->log_admin_action('ai_agent_delete', 'AI agent #' . (int)$a['id'] . ' ' . $a['name'], 'SUCCESS');
		$this->_json(array('status' => true));
	}

	public function view($id = 0)
	{
		$A = $this->ai_agent_model;
		$a = $A->agent((int)$id);
		if (!$a) { redirect('manager/ai-agents'); return; }
		$scope = $A->scope_sql($this->uid, $this->role);
		$aid = (int)$a['id'];
		$outcomes = $this->db->query("SELECT outcome name, COUNT(*) n FROM ci_ai_calls Q WHERE agent_id = {$aid} AND is_test = 0 AND status IN ('Completed', 'Failed') AND {$scope} GROUP BY outcome ORDER BY n DESC")->result_array();
		$tot = $this->db->query("SELECT COUNT(*) calls, SUM(duration > 0 AND outcome NOT IN ('Voicemail', 'No answer', 'Failed')) talked, COALESCE(SUM(duration), 0) secs, SUM(status = 'Queued') queued
			FROM ci_ai_calls Q WHERE agent_id = {$aid} AND is_test = 0 AND status != 'Cancelled' AND {$scope}")->row_array();
		$this->load->view('manager/ai_agents/view', array(
			'info' => array('title' => $a['name'], 'page_heading' => 'AI Agents'),
			'a' => $a,
			'tot' => array_map('intval', $tot),
			'outcomes' => $outcomes,
			'calls' => $A->calls("Q.agent_id = {$aid} AND Q.status != 'Queued' AND {$scope}", 100),
			'campaigns' => $this->db->select('id, name, status')->where('ai_agent_id', $aid)->order_by('id', 'DESC')->get('ci_dialer_campaigns')->result_array(),
			'prompt' => $A->build_prompt($a, array()),
			'first' => $A->first_message($a, array()),
			'can_write' => $this->_can_write(),
			'not_ready' => $A->not_ready_reason(),
			'languages' => Ai_agent_model::$languages,
			'goals' => Ai_agent_model::$goals
		));
	}

	/** One AI call with its transcript, for the drawer. */
	public function ajax_call()
	{
		$id = (int)$this->input->post('id');
		$rows = $this->ai_agent_model->calls('Q.id = ' . $id . ' AND ' . $this->ai_agent_model->scope_sql($this->uid, $this->role), 1);
		if (!$rows) return $this->_json(array('status' => false, 'message' => 'Call not found'));
		$r = $rows[0];
		$r['transcript'] = (string)$this->db->select('transcript')->where('id', $id)->get('ci_ai_calls')->row('transcript');
		$this->_json(array('status' => true, 'call' => $r));
	}

	/** A person has taken over an AI result (called back, booked, or not needed). */
	public function ajax_handled()
	{
		$id = (int)$this->input->post('id');
		$rows = $this->ai_agent_model->calls('Q.id = ' . $id . ' AND ' . $this->ai_agent_model->scope_sql($this->uid, $this->role), 1);
		if (!$rows) return $this->_json(array('status' => false, 'message' => 'Call not found'));
		$this->db->where('id', $id)->update('ci_ai_calls', array('handled_by' => $this->uid, 'handled_at' => date('Y-m-d H:i:s')));
		$this->_json(array('status' => true));
	}

	/** Test call to a phone number the user types (their own phone), with sample customer details. */
	public function ajax_test_call()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to AI agents.'));
		$A = $this->ai_agent_model;
		$a = $A->agent((int)$this->input->post('id'));
		if (!$a) return $this->_json(array('status' => false, 'message' => 'Save the agent first.'));
		$why = $A->not_ready_reason();
		if ($why !== '') return $this->_json(array('status' => false, 'message' => $why));
		$s = $A->settings();
		$phone = $A->e164((string)$this->input->post('phone'), $s['country_code']);
		if (strlen($phone) < 9 || strlen($phone) > 16) return $this->_json(array('status' => false, 'message' => 'Enter a valid phone number.'));
		$recent = (int)$this->db->where('is_test', 1)->where('queued_by', $this->uid)->where('queued_at >=', date('Y-m-d H:i:s', strtotime('-2 minutes')))->count_all_results('ci_ai_calls');
		if ($recent >= 2) return $this->_json(array('status' => false, 'message' => 'Wait a minute before the next test call.'));
		$name = (string)$this->db->select('name')->where('id', $this->uid)->get('ci_user')->row('name');
		$now = date('Y-m-d H:i:s');
		$this->db->insert('ci_ai_calls', array('agent_id' => (int)$a['id'], 'phone' => $phone, 'contact' => mb_substr(strtok($name, ' ') ?: 'there', 0, 150), 'customer' => 'Test call',
			'status' => 'Calling', 'provider' => $s['provider'], 'is_test' => 1, 'owner_id' => $this->uid, 'queued_by' => $this->uid, 'queued_at' => $now, 'started_at' => $now));
		$call = $this->db->where('id', $this->db->insert_id())->get('ci_ai_calls')->row_array();
		list($ok, $msg) = $A->start_call($a, $call, $s);
		$this->ci_admin_model->log_admin_action('ai_test_call', 'AI agent #' . (int)$a['id'] . ' test call', $ok ? 'SUCCESS' : 'FAILED');
		$this->_json(array('status' => $ok, 'message' => $ok ? 'Calling ' . $phone . ' now.' : 'The provider did not start the call: ' . $msg));
	}

	/** Queue campaign records for the campaign's AI agent. */
	public function ajax_queue_campaign()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to AI agents.'));
		$M = $this->dialer_campaign_model;
		$c = $M->get_campaign((int)$this->input->post('campaign_id'), $this->uid, $this->role);
		if (!$c || !$M->can_manage($c, $this->uid, $this->role)) return $this->_json(array('status' => false, 'message' => 'You cannot manage this campaign.'));
		if (!$M->is_dialable($c)) return $this->_json(array('status' => false, 'message' => 'The campaign is not running today.'));
		$a = $this->ai_agent_model->agent((int)$c['ai_agent_id']);
		if (!$a) return $this->_json(array('status' => false, 'message' => 'Pick an AI agent for this campaign first (Edit campaign, step 4).'));
		if ($a['status'] !== 'Active') return $this->_json(array('status' => false, 'message' => 'The AI agent "' . $a['name'] . '" is not active.'));
		$limit = max(1, min(1000, (int)$this->input->post('limit')));
		$r = $this->ai_agent_model->queue_campaign($c, $a, $limit, $this->uid);
		$this->ci_admin_model->log_admin_action('ai_queue', 'Campaign #' . (int)$c['id'] . ' queued ' . $r['added'] . ' for AI agent #' . (int)$a['id'], 'SUCCESS');
		$started = $this->ai_agent_model->dispatch(5);
		$why = $this->ai_agent_model->not_ready_reason();
		$msg = $r['added'] . ' record' . ($r['added'] === 1 ? '' : 's') . ' queued.';
		if ($r['no_phone']) $msg .= ' ' . $r['no_phone'] . ' had no usable phone number.';
		if ($why !== '') $msg .= ' Calls wait until the provider is set up: ' . $why;
		elseif (!$started && !$this->ai_agent_model->in_hours($a)) $msg .= ' Calls start in the agent\'s calling hours.';
		$this->_json(array('status' => true, 'added' => $r['added'], 'message' => $msg));
	}

	public function ajax_cancel_queue()
	{
		if (!$this->_can_write()) return $this->_json(array('status' => false, 'message' => 'You do not have Full access to AI agents.'));
		$cid = (int)$this->input->post('campaign_id');
		$aid = (int)$this->input->post('agent_id');
		if ($cid) {
			$M = $this->dialer_campaign_model;
			$c = $M->get_campaign($cid, $this->uid, $this->role);
			if (!$c || !$M->can_manage($c, $this->uid, $this->role)) return $this->_json(array('status' => false, 'message' => 'You cannot manage this campaign.'));
			$n = $this->ai_agent_model->cancel_queue(array('campaign_id' => $cid));
		} elseif ($aid) {
			$n = $this->ai_agent_model->cancel_queue(array('agent_id' => $aid));
		} else {
			return $this->_json(array('status' => false, 'message' => 'Nothing to stop'));
		}
		$this->_json(array('status' => true, 'message' => $n . ' queued call' . ($n === 1 ? '' : 's') . ' removed.'));
	}

	/** Called every 30 s while the AI pages are open, so calls flow without a cron job. */
	public function ajax_dispatch()
	{
		$started = $this->_can_write() ? $this->ai_agent_model->dispatch(5) : 0;
		$this->_json(array('status' => true, 'started' => $started, 'sum' => $this->ai_agent_model->summary($this->ai_agent_model->scope_sql($this->uid, $this->role))));
	}

	/* ---------- settings ---------- */

	public function settings()
	{
		if (!$this->_can_settings()) {
			$this->session->set_flashdata('error_message', 'AI calling settings need Full access to AI agents and Third-Party API.');
			redirect('manager/ai-agents');
			return;
		}
		$this->load->library('groq_service');
		$s = $this->ai_agent_model->settings();
		$key = $this->ai_agent_model->webhook_key();
		$this->load->view('manager/ai_agents/settings', array(
			'info' => array('title' => 'AI calling settings', 'page_heading' => 'AI Agents'),
			's' => $s,
			'has_key' => $s['api_key'] !== '',
			'providers' => Ai_agent_model::$providers,
			'webhook_url' => base_url() . 'api/ai-calls/webhook?key=' . $key,
			'cron_url' => base_url() . 'api/ai-calls/cron?key=' . $key,
			'not_ready' => $this->ai_agent_model->not_ready_reason($s),
			'groq' => $this->groq_service->is_configured()
		));
	}

	public function ajax_save_settings()
	{
		if (!$this->_can_settings()) return $this->_json(array('status' => false, 'message' => 'No access to AI calling settings.'));
		$in = $this->input;
		$data = array();
		foreach (array('provider', 'phone_number_id', 'from_number', 'country_code', 'company', 'timezone', 'concurrency', 'model', 'endpoint') as $k) $data[$k] = trim((string)$in->post($k, true));
		$data['enabled'] = (bool)$in->post('enabled');
		if ($data['endpoint'] !== '' && !preg_match('~^https://~i', $data['endpoint'])) return $this->_json(array('status' => false, 'message' => 'The endpoint must start with https://'));
		$this->ai_agent_model->save_settings($data, trim((string)$in->post('api_key')));
		$this->ci_admin_model->log_admin_action('ai_settings', 'AI calling settings saved (' . ($data['provider'] ?: 'none') . ')', 'SUCCESS');
		$why = $this->ai_agent_model->not_ready_reason();
		$this->_json(array('status' => true, 'message' => $why === '' ? 'Saved. AI calling is on.' : 'Saved. ' . $why));
	}

	public function ajax_test_connection()
	{
		if (!$this->_can_settings()) return $this->_json(array('status' => false, 'message' => 'No access'));
		list($ok, $msg) = $this->ai_agent_model->test_connection();
		$this->_json(array('status' => $ok, 'message' => $msg));
	}

	public function ajax_renew_key()
	{
		if (!$this->_can_settings()) return $this->_json(array('status' => false, 'message' => 'No access'));
		$key = $this->ai_agent_model->renew_webhook_key();
		$this->ci_admin_model->log_admin_action('ai_settings', 'AI webhook key renewed', 'SUCCESS');
		$this->_json(array('status' => true, 'webhook_url' => base_url() . 'api/ai-calls/webhook?key=' . $key, 'cron_url' => base_url() . 'api/ai-calls/cron?key=' . $key));
	}

	/* ---------- no-login endpoints (key checked in the constructor) ---------- */

	public function webhook()
	{
		$raw = file_get_contents('php://input');
		$p = json_decode((string)$raw, true);
		if (!is_array($p)) $p = $this->input->post();
		$parsed = $this->ai_agent_model->parse_webhook($p);
		if (!$parsed) return $this->_json(array('status' => true, 'ignored' => true));
		list($row, $f) = $parsed;
		$this->ai_agent_model->finish_call($row, $f);
		$this->ai_agent_model->dispatch(3);
		$this->_json(array('status' => true));
	}

	public function cron()
	{
		$this->_json(array('status' => true, 'started' => $this->ai_agent_model->dispatch(20)));
	}
}
