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
		if (strpos($method, 'ajax_') === 0 && !in_array($method, array('ajax_campaigns', 'ajax_adsets', 'ajax_forms', 'ajax_report', 'ajax_creatives'), true) && !crm_can('marketing', 'write')) {
			$this->_json(array('status' => false, 'message' => 'You can view Marketing but not change it.'));
			exit;
		}
		// which account the request is about: ?acc= on the page, conn= on ajax calls
		$cid = (int)$this->input->post('conn');
		if ($cid <= 0) $cid = (int)$this->input->get('acc');
		if ($cid > 0) $this->marketing_model->use_conn($cid);
	}

	/** The account an ajax call names (conn=), or an error answer when it names none or an unknown one. */
	private function _conn_or_fail()
	{
		$cid = (int)$this->input->post('conn');
		if ($cid > 0 && $this->marketing_model->cur_id() === $cid) return $this->marketing_model->meta();
		$this->_json(array('status' => false, 'message' => 'Save the account first.'));
		return null;
	}

	private function _hooks($c)
	{
		$api = base_url() . 'api/marketing/';
		$k = $c['webhook_key'];
		return array('meta_hook' => $k !== '' ? $api . 'meta?key=' . $k : '', 'lead_hook' => $k !== '' ? $api . 'lead?key=' . $k : '', 'verify_token' => $c['verify_token'], 'webhook_key' => $k);
	}

	private function _json($a)
	{
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($a);
	}

	private function _got_key()
	{
		return isset($_SERVER['HTTP_X_WEBHOOK_KEY']) ? (string)$_SERVER['HTTP_X_WEBHOOK_KEY'] : (string)(isset($_GET['key']) ? $_GET['key'] : '');
	}

	private function _p($k, $max = 255) { return substr(trim((string)$this->input->post($k)), 0, $max); }

	/* ---------- page ---------- */

	public function index()
	{
		$M = $this->marketing_model;
		$m = $M->meta(); // the Meta account for Posts, Meta Ads and Autopilot (?acc=)
		if ($m['platform'] !== 'meta') { $M->use_conn(0); $m = $M->meta(); }
		$conns = $M->connections();
		$metas = $M->connections('meta');
		// the account open in Connections (?edit=ID, or ?edit=new&platform=…)
		$ev = (string)$this->input->get('edit');
		$new_pf = (string)$this->input->get('platform');
		if ($ev === 'new' || !$conns) {
			$e = $M->meta();
			$e = array_merge($e, array('id' => 0, 'exists' => false, 'platform' => isset(Marketing_model::$platforms[$new_pf]) ? $new_pf : 'meta', 'name' => '', 'source_name' => '', 'ref_month' => '',
				'token' => '', 'app_secret' => '', 'webhook_key' => '', 'verify_token' => '', 'enabled' => true, 'route' => 'fixed', 'owner_id' => 0, 'rr_users' => array(), 'agents' => array(),
				'app_id' => '', 'page_id' => '', 'page_name' => '', 'ig_id' => '', 'ig_name' => '', 'ad_account_id' => '', 'website' => '', 'privacy_url' => '', 'auto_pause' => 0, 'max_cpl' => 0, 'currency' => 'AED'));
		} else {
			$e = isset($conns[(int)$ev]) ? $conns[(int)$ev] : ($m['exists'] ? $m : reset($conns));
		}
		$counts = array();
		foreach ($this->db->query("SELECT connection_id, COUNT(*) AS n, SUM(received_at >= '" . date('Y-m-01') . "') AS month FROM ci_mkt_leads GROUP BY connection_id")->result_array() as $r) $counts[(int)$r['connection_id']] = $r;
		$c = $M->claude();
		$data = array(
			'info' => array('title' => 'Marketing', 'page_heading' => 'Marketing'),
			'm' => $m, 'c' => $c, 'e' => $e, 'conns' => $conns, 'metas' => $metas, 'counts' => $counts,
			'platforms' => Marketing_model::$platforms, 'source_terms' => $M->source_terms(),
			'has_token' => $m['token'] !== '', 'has_secret' => $m['app_secret'] !== '', 'has_claude' => $c['api_key'] !== '',
			'stats' => $M->lead_stats(),
			'leads' => $M->leads('1=1', 500),
			'posts' => $m['exists'] ? $M->posts(100) : array(),
			'log' => $M->recent_log(30),
			'users' => $this->db->select('id, name')->where('status', 1)->order_by('name', 'ASC')->get('ci_user')->result_array(),
			'hooks' => $this->_hooks($e),
			'cron_url' => base_url() . 'api/marketing/cron?key=' . $M->cron_key(),
			'can_write' => crm_can('marketing', 'write'),
			'claude_models' => Marketing_model::$claude_models,
			'ctas' => Marketing_model::$ctas,
			'countries' => Marketing_model::$countries
		);
		$this->load->view('manager/marketing/index', $data);
	}

	/* ---------- accounts (connections) ---------- */

	/** Saves one account of any platform (conn=0 makes a new one). */
	public function ajax_save_conn()
	{
		$M = $this->marketing_model;
		$cid = (int)$this->input->post('conn');
		$platform = $cid > 0 ? $M->meta()['platform'] : (string)$this->input->post('platform');
		if ($cid > 0 && $M->cur_id() !== $cid) { $this->_json(array('status' => false, 'message' => 'This account no longer exists.')); return; }
		if (!isset(Marketing_model::$platforms[$platform])) { $this->_json(array('status' => false, 'message' => 'Choose the platform.')); return; }
		$fields = array('name' => $this->_p('name', 100), 'source_name' => $this->_p('source_name', 150), 'ref_month' => $this->_p('ref_month', 100));
		if ($fields['name'] === '') { $this->_json(array('status' => false, 'message' => 'Give the account a name, e.g. "Meta – Company setup page".')); return; }
		if ($fields['source_name'] === '') { $this->_json(array('status' => false, 'message' => 'Enter the Source Name used in CRM reports.')); return; }
		foreach ($M->connections() as $o) if ($o['id'] !== $cid && strcasecmp($o['name'], $fields['name']) === 0) { $this->_json(array('status' => false, 'message' => 'Another account already has this name.')); return; }
		$users = array_values(array_unique(array_filter(array_map('intval', (array)$this->input->post('rr_users')))));
		$cfg = array(
			'route' => $this->input->post('route') === 'round' ? 'round' : 'fixed',
			'owner_id' => (int)$this->input->post('owner_id'),
			'rr_users' => $users,
			'agents' => array_values(array_unique(array_filter(array_map('intval', (array)$this->input->post('agents')))))
		);
		if ($cfg['route'] === 'fixed' && $cfg['owner_id'] <= 0) { $this->_json(array('status' => false, 'message' => 'Choose who gets new leads.')); return; }
		if ($cfg['route'] === 'round' && count($users) < 1) { $this->_json(array('status' => false, 'message' => 'Choose at least one person for the rotation.')); return; }
		$token = $secret = '';
		if ($platform === 'meta') {
			$cfg += array(
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
				'auto_pause' => $this->input->post('auto_pause') ? 1 : 0,
				'max_cpl' => max(0, round((float)$this->input->post('max_cpl'), 2))
			);
			if ($cfg['auto_pause'] && $cfg['max_cpl'] <= 0) { $this->_json(array('status' => false, 'message' => 'Set the highest cost per lead for auto-pause.')); return; }
			if ($cfg['page_id'] !== '') foreach ($M->connections('meta') as $o) if ($o['id'] !== $cid && $o['page_id'] === $cfg['page_id']) { $this->_json(array('status' => false, 'message' => 'This Facebook page is already used by "' . $o['name'] . '".')); return; }
			$token = trim((string)$this->input->post('token'));
			$secret = trim((string)$this->input->post('app_secret'));
			if ($token !== '' && !preg_match('/^[A-Za-z0-9_\-|.]{20,600}$/', $token)) { $this->_json(array('status' => false, 'message' => 'That does not look like a Meta access token.')); return; }
			if ($secret !== '' && !preg_match('/^[a-f0-9]{32}$/i', $secret)) { $this->_json(array('status' => false, 'message' => 'The app secret is 32 letters and digits.')); return; }
		}
		if ($cid <= 0) $cid = $M->create_conn($platform, $fields);
		$M->save_meta($cfg, $token, $secret, (bool)$this->input->post('enabled'), $fields);
		$M->ensure_source_term($fields['source_name']);
		$M->log('settings', 'Account saved: ' . $fields['name'] . ($token !== '' ? ' (new token)' : ''), $this->uid);
		$msg = 'Saved.';
		if ($platform === 'meta' && $M->meta()['page_id'] !== '' && $M->meta()['token'] !== '') { list(, $im) = $M->detect_instagram(); $msg .= ' ' . $im; }
		$this->_json(array('status' => true, 'message' => $msg, 'id' => $cid));
	}

	/** Old name of the save call. */
	public function ajax_save_meta() { $this->ajax_save_conn(); }

	public function ajax_discover()
	{
		$M = $this->marketing_model;
		$token = trim((string)$this->input->post('token'));
		$cid = (int)$this->input->post('conn');
		if ($cid > 0 && $M->cur_id() !== $cid) { $this->_json(array('status' => false, 'message' => 'This account no longer exists.')); return; }
		if ($token !== '') {
			if (!preg_match('/^[A-Za-z0-9_\-|.]{20,600}$/', $token)) { $this->_json(array('status' => false, 'message' => 'That does not look like a Meta access token.')); return; }
			if ($cid <= 0) {
				// a new account: keep the token on its own row, switched off until it is saved
				$name = $this->_p('name', 100) !== '' ? $this->_p('name', 100) : 'Meta account ' . (count($M->connections('meta')) + 1);
				$cid = $M->create_conn('meta', array('name' => $name, 'source_name' => $this->_p('source_name', 150), 'ref_month' => $this->_p('ref_month', 100)));
				$M->save_meta(array(), $token, '', false);
			} else $M->save_meta(array(), $token, '');
		} elseif ($cid <= 0) { $this->_json(array('status' => false, 'message' => 'Paste the access token first.')); return; }
		list($ok, $r) = $M->discover();
		if (!$ok) { $this->_json(array('status' => false, 'message' => $r, 'id' => $cid)); return; }
		$this->_json(array('status' => true, 'message' => 'Connected as ' . $r['me'] . '. Choose the page and ad account, then Save.', 'data' => $r, 'id' => $cid));
	}

	public function ajax_subscribe()
	{
		if (!($c = $this->_conn_or_fail())) return;
		list($ok, $msg) = $this->marketing_model->subscribe_page();
		if ($ok) $this->marketing_model->log('subscribe', $c['name'] . ': page subscribed to lead webhook', $this->uid);
		$this->_json(array('status' => $ok, 'message' => $msg));
	}

	public function ajax_disconnect()
	{
		if (!($c = $this->_conn_or_fail())) return;
		$this->marketing_model->disconnect();
		$this->marketing_model->log('settings', $c['name'] . ': disconnected', $this->uid);
		$this->_json(array('status' => true, 'message' => 'Disconnected. The token and app secret were removed.'));
	}

	public function ajax_delete_conn()
	{
		if (!($c = $this->_conn_or_fail())) return;
		$this->marketing_model->delete_conn();
		$this->marketing_model->log('settings', 'Account removed: ' . $c['name'], $this->uid);
		$this->_json(array('status' => true, 'message' => 'Account removed. Its leads stay in the CRM.'));
	}

	public function ajax_renew_keys()
	{
		$M = $this->marketing_model;
		if ($this->input->post('what') === 'cron') {
			$M->cron_key(true);
			$M->log('settings', 'Cron key renewed', $this->uid);
			$this->_json(array('status' => true, 'cron_url' => base_url() . 'api/marketing/cron?key=' . $M->cron_key()));
			return;
		}
		if (!($c = $this->_conn_or_fail())) return;
		$M->renew_keys();
		$M->log('settings', $c['name'] . ': webhook key renewed', $this->uid);
		$this->_json(array('status' => true) + $this->_hooks($M->meta()));
	}

	public function ajax_detect_ig()
	{
		if (!($c = $this->_conn_or_fail())) return;
		list($ok, $msg) = $this->marketing_model->detect_instagram();
		$this->marketing_model->log('settings', $c['name'] . ': Instagram ' . ($ok ? 'found' : 'not linked'), $this->uid);
		$this->_json(array('status' => true, 'linked' => $ok, 'message' => $msg, 'reload' => true));
	}

	/** Earlier page posts and ad creatives to reuse. */
	public function ajax_creatives()
	{
		$c = $this->marketing_model->meta();
		if (!$c['exists'] || $c['token'] === '') { $this->_json(array('status' => false, 'rows' => array(), 'message' => 'Connect a Meta account first.')); return; }
		list($rows, $err) = $this->marketing_model->past_creatives(40);
		$this->_json(array('status' => true, 'rows' => $rows, 'message' => $rows ? '' : ($err !== '' ? $err : 'No earlier posts or ads found.'), 'note' => $err));
	}

	/* ---------- autopilot ---------- */

	public function ajax_save_autopilot()
	{
		if (!($c = $this->_conn_or_fail())) return;
		if ($c['platform'] !== 'meta') { $this->_json(array('status' => false, 'message' => 'Autopilot works with Meta accounts.')); return; }
		$M = $this->marketing_model;
		$imgs = preg_split('/\r\n|\r|\n/', trim((string)$this->input->post('ap_images')));
		foreach ($imgs as $l) if (trim($l) !== '' && !preg_match('~^https://\S+$~i', trim($l))) { $this->_json(array('status' => false, 'message' => 'Each image link must start with https:// (one a line).')); return; }
		$cfg = array(
			'ap_posts' => in_array($this->input->post('ap_posts'), array('off', 'review', 'auto'), true) ? $this->input->post('ap_posts') : 'off',
			'ap_per_week' => max(1, min(7, (int)$this->input->post('ap_per_week'))),
			'ap_time' => preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $this->_p('ap_time', 5)) ? $this->_p('ap_time', 5) : '10:00',
			'ap_topics' => substr(trim((string)$this->input->post('ap_topics')), 0, 3000),
			'ap_images' => substr(implode("\n", array_filter(array_map('trim', $imgs))), 0, 5000),
			'ap_channels' => array_values(array_intersect(array('fb', 'ig'), (array)$this->input->post('ap_channels'))),
			'ap_lang' => in_array($this->input->post('ap_lang'), array('en', 'ar', 'both'), true) ? $this->input->post('ap_lang') : 'en',
			'ap_ads' => in_array($this->input->post('ap_ads'), array('off', 'suggest', 'auto'), true) ? $this->input->post('ap_ads') : 'off',
			'ap_max_daily' => max(0, round((float)$this->input->post('ap_max_daily'), 2)),
			'ap_step' => max(5, min(50, (int)$this->input->post('ap_step'))),
			'ap_min_spend' => max(0, round((float)$this->input->post('ap_min_spend'), 2)),
			'ap_reuse' => $this->input->post('ap_reuse') ? 1 : 0
		);
		if (!$cfg['ap_channels']) $cfg['ap_channels'] = array('fb');
		if ($cfg['ap_posts'] !== 'off' && $cfg['ap_topics'] === '') { $this->_json(array('status' => false, 'message' => 'Add at least one topic for the posts.')); return; }
		if ($cfg['ap_posts'] !== 'off' && in_array('ig', $cfg['ap_channels'], true) && $cfg['ap_images'] === '' && !$cfg['ap_reuse']) { $this->_json(array('status' => false, 'message' => 'Instagram needs images: add image links or post on Facebook only.')); return; }
		if ($cfg['ap_ads'] === 'auto' && $cfg['ap_max_daily'] <= 0) { $this->_json(array('status' => false, 'message' => 'Set the most all running campaigns may spend a day before Claude can change budgets.')); return; }
		if (($cfg['ap_posts'] !== 'off' || $cfg['ap_ads'] !== 'off') && $M->claude()['api_key'] === '') { $this->_json(array('status' => false, 'message' => 'Add the Claude API key in Connections first.')); return; }
		$M->save_meta($cfg);
		$M->log('settings', $c['name'] . ': autopilot posts ' . $cfg['ap_posts'] . ', ads ' . $cfg['ap_ads'], $this->uid);
		$this->_json(array('status' => true, 'message' => 'Saved. The cron job runs it once a day.'));
	}

	public function ajax_ap_run()
	{
		if (!($c = $this->_conn_or_fail())) return;
		@set_time_limit(300);
		$M = $this->marketing_model;
		if ($this->input->post('what') === 'ads') { list($ok, $msg) = $M->ap_review_ads(true); }
		else { list($n, $msg) = $M->ap_plan_posts(true); $ok = $n > 0; }
		$this->_json(array('status' => true, 'ok' => $ok, 'message' => $msg, 'reload' => true));
	}

	public function ajax_approve_post()
	{
		$id = (int)$this->input->post('id');
		$p = $this->db->where('id', $id)->where('status', 'Review')->get('ci_mkt_posts')->row_array();
		if (!$p) { $this->_json(array('status' => false, 'message' => 'This post is no longer waiting for approval.')); return; }
		$t = strtotime((string)$p['scheduled_at']);
		$at = ($t && $t > time() + 300) ? date('Y-m-d H:i:00', $t) : date('Y-m-d H:i:00', time() + 600);
		$this->db->where('id', $id)->update('ci_mkt_posts', array('status' => 'Scheduled', 'scheduled_at' => $at));
		$this->marketing_model->log('post', 'Autopilot post #' . $id . ' approved', $this->uid);
		$this->_json(array('status' => true, 'message' => 'Approved for ' . date('d M H:i', strtotime($at)) . '.', 'reload' => true));
	}

	/* ---------- assigning and report ---------- */

	public function ajax_assign()
	{
		$owner = (int)$this->input->post('owner_id');
		$agents = (array)$this->input->post('agents');
		$ok = $owner > 0 && $this->db->where('id', $owner)->where('status', 1)->count_all_results('ci_user') === 1;
		if (!$ok) { $this->_json(array('status' => false, 'message' => 'Choose the owner.')); return; }
		$agents = array_values(array_diff(array_map('intval', array_column($this->db->select('id')->where('status', 1)->where_in('id', array_merge(array(0), array_map('intval', $agents)))->get('ci_user')->result_array(), 'id')), array($owner)));
		$n = $this->marketing_model->assign((array)$this->input->post('ids'), $owner, $agents);
		if ($n) $this->marketing_model->log('assign', $n . ' lead(s) given to user ' . $owner . (count($agents) ? ' with ' . count($agents) . ' co-worker(s)' : ''), $this->uid);
		$this->_json(array('status' => $n > 0, 'message' => $n ? $n . ' lead' . ($n === 1 ? '' : 's') . ' assigned.' : 'Nothing changed. Only new records made here can be assigned ("Already in CRM" leads belong to an existing record).', 'reload' => $n > 0));
	}

	private function _report_args()
	{
		$src = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'post' : 'get';
		$from = (string)$this->input->$src('from');
		$to = (string)$this->input->$src('to');
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-01');
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) $to = date('Y-m-d');
		if ($from > $to) list($from, $to) = array($to, $from);
		$group = in_array($this->input->$src('group'), array('owner', 'account', 'source'), true) ? $this->input->$src('group') : 'owner';
		return array($from, $to, $group, (int)$this->input->$src('account'));
	}

	public function ajax_report()
	{
		list($from, $to, $group, $acc) = $this->_report_args();
		$this->_json(array('status' => true, 'rows' => $this->marketing_model->report($from, $to, $group, $acc), 'from' => $from, 'to' => $to, 'group' => $group));
	}

	public function report_csv()
	{
		list($from, $to, $group, $acc) = $this->_report_args();
		$rows = $this->marketing_model->report($from, $to, $group, $acc);
		$head = array('owner' => 'Owner', 'account' => 'Account', 'source' => 'Source');
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="marketing-leads-' . $group . '-' . $from . '-to-' . $to . '.csv"');
		$out = fopen('php://output', 'w');
		fwrite($out, "\xEF\xBB\xBF");
		$cols = array($head[$group], 'Leads received', 'New records', 'Already in CRM', 'Not saved', 'In Database', 'In Lead', 'Further stage', 'Orders');
		if ($group === 'owner') $cols[] = 'Co-worker on';
		fputcsv($out, $cols);
		foreach ($rows as $r) {
			$line = array($r['label'], $r['received'], $r['created'], $r['dup'], $r['err'], $r['database'], $r['lead'], $r['further'], $r['orders']);
			if ($group === 'owner') $line[] = $r['co_work'];
			fputcsv($out, array_map(function ($v) { $v = (string)$v; return preg_match('/^[=+\-@]/', $v) ? "'" . $v : $v; }, $line));
		}
		fclose($out);
		$this->ci_admin_model->log_admin_action('marketing_report', 'Downloaded marketing leads report (' . $group . ', ' . $from . ' to ' . $to . ')', 'SUCCESS');
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
		@set_time_limit(300);
		list($n, $msg) = $this->marketing_model->sync_all();
		$this->_json(array('status' => true, 'new' => $n, 'message' => $msg));
	}

	public function ajax_add_lead()
	{
		$M = $this->marketing_model;
		$cid = (int)$this->input->post('conn');
		$in = array('source' => 'manual', 'full_name' => $this->_p('full_name', 150), 'phone' => $this->_p('phone', 40), 'email' => $this->_p('email', 150), 'company' => $this->_p('company'), 'city' => $this->_p('city', 100), 'campaign_name' => $this->_p('campaign_name'), 'other' => array());
		if ($cid > 0) {
			if ($M->cur_id() !== $cid) { $this->_json(array('status' => false, 'message' => 'Choose the account again.')); return; }
		} else {
			$in['no_conn'] = true;
			$in['owner'] = $this->uid; // no account: the person adding it owns it
		}
		if ((int)$this->input->post('owner_id') > 0) $in['owner'] = (int)$this->input->post('owner_id');
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
			'link' => $this->_p('link', 500),
			'image_ref' => preg_match('/^(post:[\d_]+|ad:\d+)$/', $this->_p('image_ref', 100)) ? $this->_p('image_ref', 100) : ''
		);
		if ($row['image_url'] === '') $row['image_ref'] = '';
		if (!$ch) { $this->_json(array('status' => false, 'message' => 'Choose Facebook, Instagram or both.')); return; }
		if ($row['message'] === '' && $row['image_url'] === '') { $this->_json(array('status' => false, 'message' => 'Write the post text or add an image.')); return; }
		foreach (array('image_url', 'link') as $k) if ($row[$k] !== '' && !preg_match('~^https://~i', $row[$k])) { $this->_json(array('status' => false, 'message' => 'Links must start with https://')); return; }
		if (in_array('ig', $ch, true) && $row['image_url'] === '') { $this->_json(array('status' => false, 'message' => 'Instagram posts need an image link.')); return; }
		$acc = $this->marketing_model->meta();
		if (!$acc['exists'] || $acc['platform'] !== 'meta' || (int)$this->input->post('conn') !== $acc['id']) { $this->_json(array('status' => false, 'message' => 'Connect a Meta account first.')); return; }
		if ($id) {
			$old = $this->db->where('id', $id)->get('ci_mkt_posts')->row_array();
			if (!$old || in_array($old['status'], array('Posted', 'Posting'), true)) { $this->_json(array('status' => false, 'message' => 'This post was already published.')); return; }
			if ((int)$old['connection_id'] !== $acc['id']) { $this->_json(array('status' => false, 'message' => 'This post belongs to another account.')); return; }
		}
		$row['connection_id'] = $acc['id'];
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
		$this->db->where('id', $id)->where_in('status', array('Draft', 'Scheduled', 'Failed', 'Review'))->delete('ci_mkt_posts');
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
			'image_hash' => preg_match('/^[a-f0-9]{32}$/i', $this->_p('image_hash', 32)) ? $this->_p('image_hash', 32) : '',
			'cta' => isset(Marketing_model::$ctas[$this->input->post('cta')]) ? (string)$this->input->post('cta') : 'SIGN_UP', 'start' => $this->_p('start', 20)
		);
		$err = '';
		if ($in['name'] === '') $err = 'Give the campaign a name.';
		elseif ($in['daily_budget'] < 5) $err = 'Set a daily budget (at least 5).';
		elseif (!$countries) $err = 'Choose at least one country.';
		elseif ($in['age_min'] > $in['age_max']) $err = 'The age range is reversed.';
		elseif ($in['form_id'] === '') $err = 'Choose a lead form.';
		elseif ($in['text'] === '' || $in['headline'] === '') $err = 'Write the ad text and headline (Claude can help).';
		elseif ($in['image_hash'] === '' && !preg_match('~^https://~i', $in['image_url'])) $err = 'Add an https:// link to the ad image, or pick an earlier creative.';
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

	/**
	 * Meta webhook: GET verifies the callback, POST brings new lead ids. Any Meta account's key works; each lead goes to the
	 * account whose Facebook page sent it, so several pages on one Meta app can share one callback URL.
	 */
	public function meta_webhook()
	{
		$M = $this->marketing_model;
		$kid = $M->conn_by_key($this->_got_key());
		$all = $M->connections('meta');
		if (!$kid || !isset($all[$kid])) { http_response_code(403); echo 'Invalid key'; return; }
		$m = $all[$kid];
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
		$secrets = array_values(array_unique(array_filter(array_column($all, 'app_secret'))));
		if ($secrets) {
			$sig = isset($_SERVER['HTTP_X_HUB_SIGNATURE_256']) ? (string)$_SERVER['HTTP_X_HUB_SIGNATURE_256'] : '';
			$good = false;
			foreach ($secrets as $s) if (hash_equals('sha256=' . hash_hmac('sha256', $raw, $s), $sig)) { $good = true; break; }
			if (!$good) { http_response_code(403); echo 'Bad signature'; return; }
		}
		$p = json_decode($raw, true);
		$n = 0;
		if (is_array($p) && ($p['object'] ?? '') === 'page') {
			foreach ((array)($p['entry'] ?? array()) as $e) {
				$page = (string)($e['id'] ?? '');
				$cid = $M->conn_by_page($page);
				if (!$cid) { if ($m['page_id'] !== '' && $page !== $m['page_id']) continue; $cid = $kid; }
				if (empty($all[$cid]['enabled'])) continue;
				$M->use_conn($cid);
				foreach ((array)($e['changes'] ?? array()) as $ch) {
					if (($ch['field'] ?? '') !== 'leadgen' || empty($ch['value']['leadgen_id'])) continue;
					$M->intake_meta_lead((string)$ch['value']['leadgen_id'], array('form_id' => (string)($ch['value']['form_id'] ?? '')));
					$n++;
				}
			}
		}
		$this->_json(array('status' => true, 'received' => $n));
	}

	/** Website forms, Zapier / Make, Google Ads lead form extensions, any other platform. The key says which account. */
	public function lead_webhook()
	{
		$M = $this->marketing_model;
		$raw = file_get_contents('php://input');
		$p = json_decode($raw, true);
		if (!is_array($p)) $p = $_POST;
		$google = isset($p['google_key']) && isset($p['user_column_data']);
		$cid = $M->conn_by_key($this->_got_key());
		if (!$cid && $google) $cid = $M->conn_by_key((string)$p['google_key']);
		if (!$cid) {
			http_response_code(403);
			$this->_json(array('status' => false, 'message' => 'Invalid key'));
			return;
		}
		$M->use_conn($cid);
		$c = $M->meta();
		if (!$c['enabled']) { http_response_code(403); $this->_json(array('status' => false, 'message' => 'This account is switched off in the CRM.')); return; }
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->_json(array('status' => false, 'message' => 'POST the lead as JSON or a form.')); return; }
		if ($google) {
			if (!empty($p['is_test'])) { $this->_json(array('status' => true, 'message' => 'Test received')); return; }
			$in = $M->map_google($p);
		} else {
			$g = function ($keys) use ($p) { foreach ($keys as $k) if (isset($p[$k]) && is_scalar($p[$k]) && trim((string)$p[$k]) !== '') return trim((string)$p[$k]); return ''; };
			$def = $c['platform'] === 'google' ? 'google' : ($c['platform'] === 'meta' ? 'meta' : ($c['platform'] === 'other' ? 'other' : 'web'));
			$in = array(
				'source' => in_array($g(array('source')), array('google', 'meta'), true) ? $g(array('source')) : $def,
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

	/** Every few minutes: scheduled posts, then for each Meta account: lead sync backup, cost-per-lead rule and the Claude autopilot. */
	public function cron()
	{
		$M = $this->marketing_model;
		$got = $this->_got_key();
		if ($got === '' || !hash_equals($M->cron_key(), $got)) { http_response_code(403); $this->_json(array('status' => false, 'message' => 'Invalid key')); return; }
		@set_time_limit(600);
		$out = array('status' => true, 'posted' => $M->publish_due(), 'leads' => 0, 'paused' => array(), 'autopilot' => array());
		foreach ($M->connections('meta') as $c) {
			if (!$c['enabled'] || $c['token'] === '') continue;
			$M->use_conn($c['id']);
			$m = $M->meta();
			if ($m['page_id'] !== '' && ($m['last_sync'] === '' || strtotime($m['last_sync']) < time() - 900)) { list($n) = $M->sync_leads(); $out['leads'] += $n; }
			if (!empty($m['auto_pause']) && ($m['last_rule'] === '' || strtotime($m['last_rule']) < time() - 3600)) {
				$M->save_meta(array('last_rule' => date('Y-m-d H:i:s')));
				$out['paused'] = array_merge($out['paused'], $M->auto_pause());
			}
			if ($m['page_id'] !== '' && $m['ig_id'] === '' && ($m['ig_checked'] === '' || strtotime($m['ig_checked']) < time() - 6 * 3600)) $M->detect_instagram();
			if ($m['ap_posts'] !== 'off') { list($n, $msg) = $M->ap_plan_posts(); if ($n) $out['autopilot'][] = $m['name'] . ': ' . $msg; }
			if ($m['ap_ads'] !== 'off') { list($ok, $msg) = $M->ap_review_ads(); if ($ok) $out['autopilot'][] = $m['name'] . ': ' . $msg; }
		}
		$this->_json($out);
	}
}
