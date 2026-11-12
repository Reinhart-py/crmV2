<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Calls from the Lead Finder app (signed, see Lead_finder_model):
 *   POST api/lead-finder/check  {email}               -> may this person send leads? (used for "Test link")
 *   POST api/lead-finder/stage  {email, engine, query, country_code, leads[]}
 *        -> checks every lead against the database and answers with the counts and the review page address.
 * Nothing is added to the database here; the person reviews and approves the leads in the CRM.
 */
class Lead_finder_api extends CI_Controller
{
	private function out($code, $body)
	{
		http_response_code($code);
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-store');
		echo json_encode($body);
	}

	/** Reads and verifies the signed body. Returns the decoded JSON or null (after answering). */
	private function signed($max_bytes)
	{
		if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') { $this->out(405, array('ok' => false, 'error' => 'POST only.')); return null; }
		$raw = (string)file_get_contents('php://input', false, null, 0, $max_bytes + 1);
		if (strlen($raw) > $max_bytes) { $this->out(413, array('ok' => false, 'error' => 'Too much data in one go. Send fewer leads at a time.')); return null; }
		$this->load->database();
		$this->load->library('session');
		$this->load->helper('url');
		$this->load->model(array('ci_admin_model', 'comman_model', 'lead_finder_model'));
		$h = function ($k) { return isset($_SERVER[$k]) ? (string)$_SERVER[$k] : ''; };
		$err = $this->lead_finder_model->verify($raw, $h('HTTP_X_SUITE_TIMESTAMP'), $h('HTTP_X_SUITE_SIGNATURE'));
		if ($err !== '') { $this->out(401, array('ok' => false, 'error' => $err)); return null; }
		$data = json_decode($raw, true);
		if (!is_array($data)) { $this->out(400, array('ok' => false, 'error' => 'Not JSON.')); return null; }
		return $data;
	}

	public function check()
	{
		$d = $this->signed(4096);
		if ($d === null) return;
		list($u, $why) = $this->lead_finder_model->sender(isset($d['email']) ? $d['email'] : '');
		if (!$u) { $this->out(403, array('ok' => false, 'error' => $why)); return; }
		$s = $this->lead_finder_model->settings();
		$this->out(200, array('ok' => true, 'user' => $u['name'], 'can_add_duplicates' => (bool)$u['can_force'], 'threshold' => $s['threshold'], 'max_batch' => $s['max_batch']));
	}

	public function stage()
	{
		$d = $this->signed(8 * 1048576);
		if ($d === null) return;
		list($u, $why) = $this->lead_finder_model->sender(isset($d['email']) ? $d['email'] : '');
		if (!$u) { $this->out(403, array('ok' => false, 'error' => $why)); return; }
		@set_time_limit(300);
		list($ok, $res) = $this->lead_finder_model->stage($d, $u, isset($_SERVER['HTTP_X_SUITE_DELIVERY']) ? $_SERVER['HTTP_X_SUITE_DELIVERY'] : '');
		if (!$ok) { $this->out(422, array('ok' => false, 'error' => $res)); return; }
		$this->out(200, array(
			'ok' => true,
			'batch' => 'LF-' . (int)$res['id'],
			'state' => $res['state'],
			'counts' => array('total' => (int)$res['total'], 'new' => (int)$res['n_new'], 'possible' => (int)$res['n_possible'], 'duplicate' => (int)$res['n_dup']),
			'review_url' => base_url() . 'manager/leads/lead-finder/review/' . (int)$res['id'],
		));
	}
}
