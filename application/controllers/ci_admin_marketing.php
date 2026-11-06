<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * System > Marketing (Admin only unless granted in Roles): Meta connection, leads from Meta / Google Ads /
 * website forms, Facebook and Instagram posts, Meta Ads control and the Claude writing helper.
 * meta_webhook, lead_webhook and cron are called from outside and need the marketing key.
 */
class Ci_admin_marketing extends CI_Controller
{
	private $uid = 0;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->library('session');
		$this->load->helper(array('form', 'url'));
		$this->load->model(array('ci_admin_model', 'marketing_model'));
		$this->marketing_model->ensure_schema();
		$method = $this->router->fetch_method();
		if (in_array($method, array('meta_webhook', 'lead_webhook', 'cron'), true)) return; // checked in each method
		header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->uid = (int)$this->session->userdata('manager_id');
		if ($this->uid <= 0) {
			if ($this->input->is_ajax_request()) { $this->_json(array('status' => false, 'message' => 'Please sign in again')); exit; }
			redirect('/manager/login');
			exit;
		}
		if (!crm_can('marketing', 'read')) {
			if ($this->input->is_ajax_request()) { $this->_json(array('status' => false, 'message' => 'No access to Marketing')); exit; }
			$this->session->set_flashdata('error_message', 'You do not have access to Marketing.');
			redirect(crm_landing_url());
			exit;
		}
		if (strpos($method, 'ajax_') === 0 && !in_array($method, array('ajax_campaigns', 'ajax_adsets', 'ajax_forms', 'ajax_leads'), true) && !crm_can('marketing', 'write')) {
			$this->_json(array('status' => false, 'message' => 'You can view Marketing but not change it.'));
			exit;
		}
	}

	private function _json($a)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($a);
	}

	private function _key_ok()
	{
		$want = $this->marketing_model->meta()['webhook_key'];
		$got = isset($_SERVER['HTTP_X_WEBHOOK_KEY']) ? (string)$_SERVER['HTTP_X_WEBHOOK_KEY'] : (string)(isset($_GET['key']) ? $_GET['key'] : '');
		return $want !== '' && $got !== '' && hash_equals($want, $got);
	}

	private function _p($k, $max = 255) { return substr(trim((string)$this->input->post($k)), 0, $max); }

	/* ---------- page ---------- */

	public function index()
	{
		$M = $this->marketing_model;
		$key = $M->webhook_key();
		$m = $M->meta();
		$c = $M->claude();
		$api = base_url() . 'api/marketing/';
		$data = array(
			'info' => array('title' => 'Marketing', 'page_heading' => 'Marketing'),
			'm' => $m, 'c' => $c,
			'has_token' => $m['token'] !== '', 'has_secret' => $m['app_secret'] !== '', 'has_claude' => $c['api_key'] !== '',
			'stats' => $M->lead_stats(),
			'leads' => $M->leads('1=1', 300),
			'posts' => $M->posts(100),
			'log' => $M->recent_log(30),
			'users' => $this->db->select('id, name')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array(),
			'meta_hook' => $api . 'meta?key=' . $key,
			'lead_hook' => $api . 'lead?key=' . $key,
			'cron_url' => $api . 'cron?key=' . $key,
			'webhook_key' => $key,
			'can_write' => crm_can('marketing', 'write'),
			'claude_models' => Marketing_model::$claude_models,
			'ctas' => Marketing_model::$ctas,
			'countries' => Marketing_model::$countries
		);
		$this->load->view('manager/marketing/index', $data);
	}

	/* ---------- connections ---------- */

	public function ajax_save_meta()
	{
		$M = $this->marketing_model;
		$users = array_values(array_unique(array_filter(array_map('intval', (array)$this->input->post('rr_users')))));
		$cfg = array(
			'app_id' => preg_replace('/\D/', '', $this->_p('app_id', 30)),
			'graph_version' => preg_match('/^v\d{2}\.0$/', $this->_p('graph_version', 8)) ? $this->_p('graph_version', 8) : 'v23.0',
			'page_id' => preg_replace('/\D/', '', $this->_p('page_id', 30)),
			'page_name' => $this->_p('page_name', 150),
			'ig_id' => preg_replace('/\D/', '', $this->_p('ig_id', 30)),
			'ig_name' => $this->_p('ig_name', 100),
			'ad_account_id' => preg_replace('/\D/', '', $this->_p('ad_account_id', 30)),
			'currency' => preg_match('/^[A-Z]{3}$/', $this->_p('currency', 3)) ? $this->_p('currency', 3) : 'AED',
			'website' => preg_match('~^https?://~i', $this->_p('website', 255)) ? $this->_p('website', 255) : '',
			'privacy_url' => preg_match('~^https?://~i', $this->_p('privacy_url', 255)) ? $this->_p('privacy_url', 255) : '',
			'route' => $this->input->post('route') === 'round' ? 'round' : 'fixed',
			'owner_id' => (int)$this->input->post('owner_id'),
			'rr_users' => $users,
			'auto_pause' => $this->input->post('auto_pause') ? 1 : 0,
			'max_cpl' => max(0, round((float)$this->input->post('max_cpl'), 2))
		);
		if ($cfg['route'] === 'fixed' && $cfg['owner_id'] <= 0) { $this->_json(array('status' => false, 'message' => 'Choose who gets new leads.')); return; }
		if ($cfg['route'] === 'round' && count($users) < 1) { $this->_json(array('status' => false, 'message' => 'Choose at least one person for the rotation.')); return; }
		if ($cfg['auto_pause'] && $cfg['max_cpl'] <= 0) { $this->_json(array('status' => false, 'message' => 'Set the highest cost per lead for auto-pause.')); return; }
		$token = trim((string)$this->input->post('token'));
		$secret = trim((string)$this->input->post('app_secret'));
		if ($token !== '' && !preg_match('/^[A-Za-z0-9_\-|.]{20,600}$/', $token)) { $this->_json(array('status' => false, 'message' => 'That does not look like a Meta access token.')); return; }
		if ($secret !== '' && !preg_match('/^[a-f0-9]{32}$/i', $secret)) { $this->_json(array('status' => false, 'message' => 'The app secret is 32 letters and digits.')); return; }
		$M->save_meta($cfg, $token, $secret, true);
		$M->log('settings', 'Meta connection saved' . ($token !== '' ? ' (new token)' : ''), $this->uid);
		$this->_json(array('status' => true, 'message' => 'Saved.'));
	}

	public function ajax_discover()
	{
		$M = $this->marketing_model;
		$token = trim((string)$this->input->post('token'));
		if ($token !== '') {
			if (!preg_match('/^[A-Za-z0-9_\-|.]{20,600}$/', $token)) { $this->_json(array('status' => false, 'message' => 'That does not look like a Meta access token.')); return; }
			$M->save_meta(array(), $token, '', true);
		}
		list($ok, $r) = $M->discover();
		if (!$ok) { $this->_json(array('status' => false, 'message' => $r)); return; }
		$this->_json(array('status' => true, 'message' => 'Connected as ' . $r['me'] . '. Choose the page and ad account, then Save.', 'data' => $r));
	}

	public function ajax_subscribe()
	{
		list($ok, $msg) = $this->marketing_model->subscribe_page();
		if ($ok) $this->marketing_model->log('subscribe', 'Page subscribed to lead webhook', $this->uid);
		$this->_json(array('status' => $ok, 'message' => $msg));
	}

	public function ajax_disconnect()
	{
		$this->db->where('service_name', 'meta')->update('ci_integration_settings', array('api_key' => '', 'api_secret' => '', 'status' => 0, 'updated_at' => date('Y-m-d H:i:s')));
		$this->marketing_model->log('settings', 'Meta disconnected', $this->uid);
		$this->_json(array('status' => true, 'message' => 'Meta disconnected. The token and app secret were removed.'));
	}

	public function ajax_renew_keys()
	{
		$M = $this->marketing_model;
		$M->renew_keys();
		$m = $M->meta();
		$api = base_url() . 'api/marketing/';
		$M->log('settings', 'Webhook key renewed', $this->uid);
		$this->_json(array('status' => true, 'meta_hook' => $api . 'meta?key=' . $m['webhook_key'], 'lead_hook' => $api . 'lead?key=' . $m['webhook_key'], 'cron_url' => $api . 'cron?key=' . $m['webhook_key'], 'verify_token' => $m['verify_token'], 'webhook_key' => $m['webhook_key']));
	}

	public function ajax_save_claude()
	{
		$key = trim((string)$this->input->post('api_key'));
		if ($key !== '' && !preg_match('/^sk-ant-[A-Za-z0-9_\-]{20,200}$/', $key)) { $this->_json(array('status' => false, 'message' => 'Claude API keys start with sk-ant-.')); return; }
		$this->marketing_model->save_claude(array('model' => (string)$this->input->post('model'), 'brand' => (string)$this->input->post('brand'), 'tone' => (string)$this->input->post('tone')), $key);
		$this->marketing_model->log('settings', 'Claude settings saved' . ($key !== '' ? ' (new key)' : ''), $this->uid);
		$this->_json(array('status' => true, 'message' => 'Saved.'));
	}

	public function ajax_test_claude()
	{
		list($ok, $msg) = $this->marketing_model->test_claude();
		$this->_json(array('status' => $ok, 'message' => $msg));
	}

	/* ---------- leads ---------- */

	public function ajax_sync_leads()
	{
		list($n, $msg) = $this->marketing_model->sync_leads();
		$this->_json(array('status' => true, 'new' => $n, 'message' => $msg));
	}

	public function ajax_add_lead()
	{
		$in = array('source' => 'manual', 'full_name' => $this->_p('full_name', 150), 'phone' => $this->_p('phone', 40), 'email' => $this->_p('email', 150), 'company' => $this->_p('company'), 'city' => $this->_p('city', 100), 'campaign_name' => $this->_p('campaign_name'), 'other' => array());
		if ($this->_p('note', 500) !== '') $in['other']['note'] = $this->_p('note', 500);
		if ($in['email'] !== '' && !filter_var($in['email'], FILTER_VALIDATE_EMAIL)) { $this->_json(array('status' => false, 'message' => 'The e-mail is not valid.')); return; }
		list($st) = $this->marketing_model->intake($in);
		$msg = array('Created' => 'Lead added to the CRM.', 'Duplicate' => 'This phone or e-mail is already in the CRM; logged as a duplicate.', 'Error' => 'Enter a phone or e-mail.');
		$this->_json(array('status' => $st !== 'Error', 'message' => isset($msg[$st]) ? $msg[$st] : $st));
	}

	/* ---------- posts ---------- */

	public function ajax_save_post()
	{
		$id = (int)$this->input->post('id');
		$ch = array_values(array_intersect(array('fb', 'ig'), (array)$this->input->post('channels')));
		$act = (string)$this->input->post('act');
		$row = array(
			'channels' => implode(',', $ch),
			'message' => substr(trim((string)$this->input->post('message')), 0, 5000),
			'image_url' => $this->_p('image_url', 500),
			'link' => $this->_p('link', 500)
		);
		if (!$ch) { $this->_json(array('status' => false, 'message' => 'Choose Facebook, Instagram or both.')); return; }
		if ($row['message'] === '' && $row['image_url'] === '') { $this->_json(array('status' => false, 'message' => 'Write the post text or add an image.')); return; }
		foreach (array('image_url', 'link') as $k) if ($row[$k] !== '' && !preg_match('~^https://~i', $row[$k])) { $this->_json(array('status' => false, 'message' => 'Links must start with https://')); return; }
		if (in_array('ig', $ch, true) && $row['image_url'] === '') { $this->_json(array('status' => false, 'message' => 'Instagram posts need an image link.')); return; }
		if ($id) {
			$old = $this->db->where('id', $id)->get('ci_mkt_posts')->row_array();
			if (!$old || in_array($old['status'], array('Posted', 'Posting'), true)) { $this->_json(array('status' => false, 'message' => 'This post was already published.')); return; }
		}
		if ($act === 'schedule') {
			$t = strtotime((string)$this->input->post('scheduled_at'));
			if (!$t || $t < time() + 60) { $this->_json(array('status' => false, 'message' => 'Pick a time in the future.')); return; }
			$row['status'] = 'Scheduled';
			$row['scheduled_at'] = date('Y-m-d H:i:00', $t);
		} else {
			$row['status'] = 'Draft';
			$row['scheduled_at'] = null;
		}
		$row['error'] = '';
		if ($id) $this->db->where('id', $id)->update('ci_mkt_posts', $row);
		else {
			$row['created_by'] = $this->uid;
			$row['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_mkt_posts', $row);
			$id = (int)$this->db->insert_id();
		}
		if ($act === 'now') {
			$this->db->where('id', $id)->update('ci_mkt_posts', array('status' => 'Posting'));
			list($ok, $msg) = $this->marketing_model->publish($this->db->where('id', $id)->get('ci_mkt_posts')->row_array());
			$this->marketing_model->log('post', ($ok ? 'Posted' : 'Post failed') . ' #' . $id, $this->uid);
			$this->_json(array('status' => $ok, 'message' => $msg, 'reload' => true));
			return;
		}
		$this->_json(array('status' => true, 'message' => $act === 'schedule' ? 'Scheduled for ' . date('d M H:i', strtotime($row['scheduled_at'])) . '.' : 'Draft saved.', 'reload' => true));
	}

	public function ajax_delete_post()
	{
		$id = (int)$this->input->post('id');
		$this->db->where('id', $id)->where_in('status', array('Draft', 'Scheduled', 'Failed'))->delete('ci_mkt_posts');
		$this->_json(array('status' => $this->db->affected_rows() === 1, 'message' => $this->db->affected_rows() === 1 ? 'Deleted from the CRM.' : 'Published posts stay; delete them on Facebook / Instagram.'));
	}

	/** Claude writes a post, an ad or ad text ideas. */
	public function ajax_ai_write()
	{
		$M = $this->marketing_model;
		$kind = (string)$this->input->post('kind');
		$topic = substr(trim((string)$this->input->post('topic')), 0, 1500);
		$lang = $this->input->post('language') === 'ar' ? 'Arabic' : ($this->input->post('language') === 'both' ? 'English, then the same text in Arabic' : 'English');
		if ($topic === '') { $this->_json(array('status' => false, 'message' => 'Say what the post or ad is about.')); return; }
		if ($kind === 'ad') {
			$prompt = "Write a Meta lead ad in {$lang} for: {$topic}\n\nReturn exactly two parts separated by a line containing only ---\n1. Primary text: up to 300 characters, one clear benefit, ends with a call to fill the form.\n2. Headline: up to 40 characters.";
		} else {
			$ch = (array)$this->input->post('channels');
			$where = in_array('ig', $ch, true) ? (in_array('fb', $ch, true) ? 'Facebook and Instagram' : 'Instagram') : 'Facebook';
			$prompt = "Write one {$where} post in {$lang} about: {$topic}\n\nUp to 120 words, short paragraphs, 1-3 fitting emojis at most, a clear call to action, then 3-6 relevant hashtags on the last line.";
		}
		list($ok, $text) = $M->ask_claude($M->claude_system(), $prompt, 4000);
		if (!$ok) { $this->_json(array('status' => false, 'message' => $text)); return; }
		$out = array('status' => true, 'text' => $text);
		if ($kind === 'ad') {
			$parts = preg_split('/^\s*-{3,}\s*$/m', $text);
			$clean = function ($s) { return trim(preg_replace('/^\s*(\d\.\s*)?(primary text|headline)\s*:\s*/i', '', trim($s))); };
			$out['primary'] = $clean($parts[0]);
			$out['headline'] = isset($parts[1]) ? substr($clean($parts[1]), 0, 40) : '';
		}
		$this->_json($out);
	}

	/* ---------- Meta Ads ---------- */

	public function ajax_campaigns()
	{
		list($ok, $rows, $err) = $this->marketing_model->campaigns((string)$this->input->post('preset'));
		$this->_json(array('status' => $ok, 'rows' => $rows, 'message' => $err, 'currency' => $this->marketing_model->meta()['currency']));
	}

	public function ajax_adsets()
	{
		list($ok, $rows, $err) = $this->marketing_model->adsets(preg_replace('/\D/', '', (string)$this->input->post('campaign_id')));
		$this->_json(array('status' => $ok, 'rows' => $rows, 'message' => $err));
	}

	public function ajax_forms()
	{
		list($ok, $rows, $err) = $this->marketing_model->forms();
		$this->_json(array('status' => $ok, 'rows' => $rows, 'message' => $err));
	}

	public function ajax_ad_status()
	{
		$id = preg_replace('/\D/', '', (string)$this->input->post('object_id'));
		$st = (string)$this->input->post('status');
		list($ok, $msg) = $this->marketing_model->set_status($id, $st);
		if ($ok) $this->marketing_model->log('ad_status', $this->_p('name') . ' → ' . ($st === 'ACTIVE' ? 'running' : 'paused'), $this->uid);
		$this->_json(array('status' => $ok, 'message' => $msg));
	}

	public function ajax_ad_budget()
	{
		$id = preg_replace('/\D/', '', (string)$this->input->post('object_id'));
		$amount = (float)$this->input->post('amount');
		list($ok, $msg) = $this->marketing_model->set_budget($id, $amount);
		if ($ok) $this->marketing_model->log('ad_budget', $this->_p('name') . ' → ' . number_format($amount, 2) . ' a day', $this->uid);
		$this->_json(array('status' => $ok, 'message' => $msg));
	}

	public function ajax_create_form()
	{
		$q = array_values(array_intersect(array('FULL_NAME', 'PHONE', 'EMAIL', 'COMPANY_NAME', 'CITY', 'JOB_TITLE'), (array)$this->input->post('questions')));
		$name = $this->_p('name', 100);
		if ($name === '') { $this->_json(array('status' => false, 'message' => 'Give the form a name.')); return; }
		list($ok, $r) = $this->marketing_model->create_form($name, $this->_p('privacy_url'), $q, $this->_p('thanks', 300));
		if ($ok) $this->marketing_model->log('form', 'Lead form created: ' . $name, $this->uid);
		$this->_json(array('status' => $ok, 'message' => $ok ? 'Form created.' : $r, 'id' => $ok ? $r : ''));
	}

	public function ajax_create_campaign()
	{
		$countries = array_values(array_intersect(array_keys(Marketing_model::$countries), (array)$this->input->post('countries')));
		$in = array(
			'name' => $this->_p('name', 150), 'daily_budget' => round((float)$this->input->post('daily_budget'), 2),
			'countries' => $countries, 'age_min' => max(18, min(65, (int)$this->input->post('age_min'))), 'age_max' => max(18, min(65, (int)$this->input->post('age_max'))),
			'form_id' => preg_replace('/\D/', '', (string)$this->input->post('form_id')), 'text' => substr(trim((string)$this->input->post('text')), 0, 2000),
			'headline' => $this->_p('headline', 40), 'image_url' => $this->_p('image_url', 500),
			'cta' => isset(Marketing_model::$ctas[$this->input->post('cta')]) ? (string)$this->input->post('cta') : 'SIGN_UP', 'start' => $this->_p('start', 20)
		);
		$err = '';
		if ($in['name'] === '') $err = 'Give the campaign a name.';
		elseif ($in['daily_budget'] < 5) $err = 'Set a daily budget (at least 5).';
		elseif (!$countries) $err = 'Choose at least one country.';
		elseif ($in['age_min'] > $in['age_max']) $err = 'The age range is reversed.';
		elseif ($in['form_id'] === '') $err = 'Choose a lead form.';
		elseif ($in['text'] === '' || $in['headline'] === '') $err = 'Write the ad text and headline (Claude can help).';
		elseif (!preg_match('~^https://~i', $in['image_url'])) $err = 'Add an https:// link to the ad image.';
		if ($err !== '') { $this->_json(array('status' => false, 'message' => $err)); return; }
		list($ok, $msg, $cid) = $this->marketing_model->create_lead_campaign($in);
		if ($ok) $this->marketing_model->log('campaign', 'Lead campaign created (paused): ' . $in['name'] . ', ' . number_format($in['daily_budget'], 2) . ' a day', $this->uid);
		$this->_json(array('status' => $ok, 'message' => $msg, 'id' => $cid));
	}

	/** Claude looks at the campaign numbers and says what to change. */
	public function ajax_ai_review()
	{
		$M = $this->marketing_model;
		list($ok, $rows, $err) = $M->campaigns('last_7d');
		if (!$ok) { $this->_json(array('status' => false, 'message' => $err)); return; }
		if (!$rows) { $this->_json(array('status' => false, 'message' => 'There are no campaigns to review yet.')); return; }
		$cur = $M->meta()['currency'];
		$lines = array();
		foreach ($rows as $r) {
			$lines[] = sprintf('- %s | %s | budget %s/day | spend %s %.2f | %d impressions | %d clicks | CTR %.2f%% | %d leads | CPL %s',
				$r['name'], $r['status'], $r['daily_budget'] !== null ? number_format($r['daily_budget'], 2) : 'ad-set level', $cur, $r['spend'], $r['impressions'], $r['clicks'], $r['ctr'], $r['leads'], $r['cpl'] !== null ? $cur . ' ' . $r['cpl'] : 'n/a');
		}
		$crm = $M->lead_stats();
		$prompt = "Meta lead campaigns, last 7 days:\n" . implode("\n", $lines) . "\n\nCRM this month: {$crm['meta']} Meta leads, {$crm['dup']} were already in the CRM.\n\n"
			. "Give a short review for the business owner: at most 6 bullet points, each naming the campaign and one concrete action (pause, raise or lower the budget by an amount, change the audience, refresh the creative, or keep). Plain text with '- ' bullets, no headings.";
		list($ok, $text) = $M->ask_claude("You are a careful Meta Ads performance marketer for a B2B company in the UAE. Base advice only on the numbers given; say when there is too little data to judge.", $prompt, 6000);
		$this->_json(array('status' => $ok, 'text' => $ok ? $text : '', 'message' => $ok ? '' : $text));
	}

	/* ---------- outside callers ---------- */

	/** Meta webhook: GET verifies the callback, POST brings new lead ids. */
	public function meta_webhook()
	{
		$M = $this->marketing_model;
		if (!$this->_key_ok()) { http_response_code(403); echo 'Invalid key'; return; }
		$m = $M->meta();
		if ($_SERVER['REQUEST_METHOD'] === 'GET') {
			$mode = isset($_GET['hub_mode']) ? $_GET['hub_mode'] : '';
			$tok = isset($_GET['hub_verify_token']) ? (string)$_GET['hub_verify_token'] : '';
			if ($mode === 'subscribe' && $m['verify_token'] !== '' && hash_equals($m['verify_token'], $tok)) {
				header('Content-Type: text/plain');
				echo preg_replace('/[^\w\-.]/', '', (string)(isset($_GET['hub_challenge']) ? $_GET['hub_challenge'] : ''));
				return;
			}
			http_response_code(403);
			echo 'Verify token mismatch';
			return;
		}
		$raw = file_get_contents('php://input');
		if ($m['app_secret'] !== '') {
			$sig = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256']) ? (string)$_SERVER['HTTP_X_HUB_SIGNATURE_256'] : '';
			if (!hash_equals('sha256=' . hash_hmac('sha256', $raw, $m['app_secret']), $sig)) { http_response_code(403); echo 'Bad signature'; return; }
		}
		$p = json_decode($raw, true);
		$n = 0;
		if (is_array($p) && ($p['object'] ?? '') === 'page') {
			foreach ((array)($p['entry'] ?? array()) as $e) {
				if ($m['page_id'] !== '' && (string)($e['id'] ?? '') !== $m['page_id']) continue;
				foreach ((array)($e['changes'] ?? array()) as $ch) {
					if (($ch['field'] ?? '') !== 'leadgen' || empty($ch['value']['leadgen_id'])) continue;
					$M->intake_meta_lead((string)$ch['value']['leadgen_id'], array('form_id' => (string)($ch['value']['form_id'] ?? '')));
					$n++;
				}
			}
		}
		$this->_json(array('status' => true, 'received' => $n));
	}

	/** Website forms, Zapier / Make, Google Ads lead form extensions. */
	public function lead_webhook()
	{
		$M = $this->marketing_model;
		$raw = file_get_contents('php://input');
		$p = json_decode($raw, true);
		if (!is_array($p)) $p = $_POST;
		$google = isset($p['google_key']) && isset($p['user_column_data']);
		$key = $M->meta()['webhook_key'];
		if (!$this->_key_ok() && !($google && $key !== '' && hash_equals($key, (string)$p['google_key']))) {
			http_response_code(403);
			$this->_json(array('status' => false, 'message' => 'Invalid key'));
			return;
		}
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->_json(array('status' => false, 'message' => 'POST the lead as JSON or a form.')); return; }
		if ($google) {
			if (!empty($p['is_test'])) { $this->_json(array('status' => true, 'message' => 'Test received')); return; }
			$in = $M->map_google($p);
		} else {
			$g = function ($keys) use ($p) { foreach ($keys as $k) if (isset($p[$k]) && is_scalar($p[$k]) && trim((string)$p[$k]) !== '') return trim((string)$p[$k]); return ''; };
			$in = array(
				'source' => in_array($g(array('source')), array('google', 'meta'), true) ? $g(array('source')) : 'web',
				'external_id' => substr($g(array('id', 'lead_id', 'external_id')), 0, 64),
				'full_name' => $g(array('full_name', 'name', 'your-name', 'fullname')), 'phone' => $g(array('phone', 'mobile', 'phone_number', 'your-phone', 'tel')),
				'email' => $g(array('email', 'your-email', 'mail')), 'company' => $g(array('company', 'company_name', 'organization')), 'city' => $g(array('city', 'emirate')),
				'campaign_name' => $g(array('campaign', 'utm_campaign')), 'form_name' => $g(array('form', 'form_name', 'page')), 'other' => array()
			);
			foreach (array('message', 'your-message', 'service', 'utm_source', 'utm_medium') as $k) if ($g(array($k)) !== '') $in['other'][$k] = substr($g(array($k)), 0, 500);
			if ($in['email'] !== '' && !filter_var($in['email'], FILTER_VALIDATE_EMAIL)) $in['email'] = '';
		}
		list($st, $id) = $M->intake($in);
		$this->_json(array('status' => $st !== 'Error', 'result' => $st, 'id' => $id));
	}

	/** Every few minutes: scheduled posts, lead sync backup, cost-per-lead rule. */
	public function cron()
	{
		if (!$this->_key_ok()) { http_response_code(403); $this->_json(array('status' => false, 'message' => 'Invalid key')); return; }
		$M = $this->marketing_model;
		$m = $M->meta();
		$out = array('status' => true, 'posted' => 0, 'leads' => 0, 'paused' => array());
		if ($m['token'] === '' || !$m['enabled']) { $out['message'] = 'Meta not connected'; $this->_json($out); return; }
		$out['posted'] = $M->publish_due();
		if ($m['last_sync'] === '' || strtotime($m['last_sync']) < time() - 900) list($out['leads']) = $M->sync_leads();
		$last_rule = (string)$this->db->query("SELECT MAX(created_at) AS t FROM ci_mkt_log WHERE action = 'rule_check'")->row()->t;
		if (!empty($m['auto_pause']) && ($last_rule === '' || strtotime($last_rule) < time() - 3600)) {
			$this->db->where('action', 'rule_check')->delete('ci_mkt_log');
			$M->log('rule_check', 'Cost-per-lead rule checked');
			$out['paused'] = $M->auto_pause();
		}
		$this->_json($out);
	}
}
