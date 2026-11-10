<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ci_admin_integrations extends CI_Controller {

	public function __construct() 
	{
		parent::__construct();
		$this->load->library(array('Session'));
		$this->load->helper(array('form', 'url', 'text'));
		$this->load->model(array('ci_admin_model', 'comman_model'));
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
		header("Cache-Control: no-store, no-cache, must-revalidate"); 
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header('Cache-Control: no-cache');
	}

	public function if_not_login($url = '')
	{
		if ($this->session->userdata('manager_id') == '') {
			if ($url != '') {
				redirect($url);
			} else {
				redirect('/manager/login');
			}
		}
	}

	public function index()
	{
		$this->if_not_login();
		$manager_role = (int)$this->session->userdata('manager_role');
		if (!crm_can('integrations')) {
			$this->session->set_flashdata('error_message', 'Unauthorized access to Third-Party Integrations.');
			redirect('manager/dashboard');
			return;
		}

		$data = array();
		$data['info'] = array(
			'title' => 'Third-Party API Integrations',
			'page_heading' => 'Third-Party API Integrations'
		);

		$all_rows = $this->db->get('ci_integration_settings')->result_array();
		$settings = array();
		foreach ($all_rows as $row) {
			$cfg = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
			$row['config'] = is_array($cfg) ? $cfg : array();
			$settings[$row['service_name']] = $row;
		}

		$default_services = array('groq', 'pbx_3cx', 'email_smtp', 'whatsapp', 'schedule_cron', 'helpdesk', 'subscription');
		foreach ($default_services as $srv) {
			if (!isset($settings[$srv])) {
				$settings[$srv] = array(
					'id' => 0,
					'service_name' => $srv,
					'api_key' => '',
					'api_secret' => '',
					'endpoint' => '',
					'config_json' => '{}',
					'config' => array(),
					'status' => 0,
					'updated_at' => null
				);
			}
		}

		$data['settings'] = $settings;
		$data['can_edit'] = crm_can('integrations', 'write');
		$this->load->view('manager/system/integrations', $data);
	}

	public function save()
	{
		$this->if_not_login();
		$manager_role = (int)$this->session->userdata('manager_role');
		if (!crm_can('integrations', 'write')) {
			$this->session->set_flashdata('error_message', 'You need Full access to Third-Party API to change these settings.');
			redirect('manager/dashboard');
			return;
		}

		if ($this->input->server('REQUEST_METHOD') !== 'POST') {
			redirect('manager/system/integrations');
			return;
		}

		$service = trim($this->input->post('service_name', true));
		$endpoint = trim($this->input->post('endpoint', true));
		$api_key = trim($this->input->post('api_key', true));
		$api_secret = trim($this->input->post('api_secret', true));
		$status = (int)$this->input->post('status', true);
		$config = $this->input->post('config');
		if (!is_array($config)) {
			$config = array();
		}

		if (empty($service)) {
			$this->session->set_flashdata('error_message', 'Invalid service name.');
			redirect('manager/system/integrations');
			return;
		}

		$existing = $this->db->where('service_name', $service)->get('ci_integration_settings')->row_array();
		if (empty($api_key) && !empty($existing['api_key'])) {
			$api_key = $existing['api_key'];
		}
		if (empty($api_secret) && !empty($existing['api_secret'])) {
			$api_secret = $existing['api_secret'];
		}

		// Password-type config fields are shown empty; keep the stored value when left blank.
		$old_cfg = !empty($existing['config_json']) ? json_decode($existing['config_json'], true) : array();
		foreach (array('smtp_pass', 'bearer_token') as $secret_key) {
			if (array_key_exists($secret_key, $config) && $config[$secret_key] === '' && !empty($old_cfg[$secret_key])) {
				$config[$secret_key] = $old_cfg[$secret_key];
			}
		}

		$data_update = array(
			'endpoint' => $endpoint,
			'api_key' => $api_key,
			'api_secret' => $api_secret,
			'config_json' => json_encode($config),
			'status' => $status,
			'updated_at' => date('Y-m-d H:i:s')
		);

		if (!empty($existing)) {
			$this->db->where('id', $existing['id'])->update('ci_integration_settings', $data_update);
		} else {
			$data_update['service_name'] = $service;
			$data_update['created_at'] = date('Y-m-d H:i:s');
			$this->db->insert('ci_integration_settings', $data_update);
		}

		$this->session->set_flashdata('message', strtoupper($service) . ' integration settings saved successfully.');
		redirect('manager/system/integrations');
	}

	/** Sends a real test email with the active SMTP settings to the signed-in admin. */
	public function ajax_send_test_email()
	{
		$this->if_not_login();
		header('Content-Type: application/json');
		$manager_role = (int)$this->session->userdata('manager_role');
		if (!crm_can('integrations', 'write')) {
			echo json_encode(array('status' => false, 'message' => 'You need Full access to Third-Party API.'));
			return;
		}
		$to = trim((string)$this->input->post('to_email', true));
		if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('status' => false, 'message' => 'Enter a valid email address to send the test to.'));
			return;
		}
		$this->load->model('mail_model');
		$cfg = $this->mail_model->smtp_config();
		$resp = $this->mail_model->sendmail($to, 'Sales CRM test email', array(), '<p>This is a test email from Sales CRM.</p><p>If you can read this, password reset emails will be delivered the same way.</p>');
		if ($resp === 'ok') {
			echo json_encode(array('status' => true, 'message' => 'Test email sent to ' . $to . ' via ' . $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['source'] . '). Check the inbox and spam folder.'));
		} else {
			echo json_encode(array('status' => false, 'message' => 'Sending failed via ' . $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['source'] . '): ' . $this->mail_model->last_error));
		}
	}

	public function ajax_test_connection()
	{
		$this->if_not_login();
		if (!crm_can('integrations', 'write')) {
			echo json_encode(array('status' => false, 'message' => 'You need Full access to Third-Party API.'));
			return;
		}
		$service = trim($this->input->post('service_name', true));

		if (empty($service)) {
			echo json_encode(array('status' => false, 'message' => 'No service specified'));
			return;
		}

		$row = $this->db->where('service_name', $service)->get('ci_integration_settings')->row_array();
		if (empty($row)) {
			echo json_encode(array('status' => false, 'message' => 'Service not configured yet'));
			return;
		}

		$config = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
		if (!is_array($config)) {
			$config = array();
		}

		if ($service === 'subscription') {
			// Ask the 7strokes portal now (does not switch the check on or off)
			$this->load->model('subscription_model');
			if (!$this->subscription_model->enabled($this->subscription_model->row())) {
				$r2 = $this->subscription_model->row();
				$c2 = isset($r2['config']) ? $r2['config'] : array();
				if (trim((string)$row['endpoint']) === '' || trim((string)$row['api_secret']) === '' || empty($c2['account_email'])) {
					echo json_encode(array('status' => false, 'message' => 'Fill in the portal address, app secret and account email, then save.'));
					return;
				}
				$ans = $this->subscription_model->ask($r2);
			} else {
				$ans = $this->subscription_model->state(true);
			}
			if (!empty($ans['allowed'])) {
				$txt = 'Subscription active';
				if (!empty($ans['role']) && $ans['role'] === 'staff') $txt .= ' (portal staff account)';
				if (!empty($ans['plan'])) $txt .= ', plan ' . $ans['plan'] . (!empty($ans['cycle']) ? ' (' . $ans['cycle'] . ')' : '');
				if (!empty($ans['status'])) $txt .= ', ' . $ans['status'];
				if (!empty($ans['validUntil'])) $txt .= ', valid until ' . date('d M Y', strtotime($ans['validUntil']));
				echo json_encode(array('status' => true, 'message' => $txt . '.' . ((int)$row['status'] === 1 ? '' : ' The check is off; set Status to On and save to make the CRM follow it.')));
			} else {
				echo json_encode(array('status' => false, 'message' => !empty($ans['reason']) ? $ans['reason'] : 'The portal says the subscription is not active.'));
			}
			return;
		}

		if ($service === 'groq') {
			$key = !empty($row['api_key']) ? $row['api_key'] : getenv('GROQ_API_KEY');
			if (empty($key)) {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 0));
				echo json_encode(array('status' => false, 'message' => 'No Groq API key found in database or GROQ_API_KEY environment variable.'));
				return;
			}
			$ch = curl_init('https://api.groq.com/openai/v1/models');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $key));
			curl_setopt($ch, CURLOPT_TIMEOUT, 8);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$res = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$err = curl_error($ch);
			curl_close($ch);

			if ($code === 200) {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 1));
				echo json_encode(array('status' => true, 'message' => 'Groq API connection confirmed. Speech and AI inference are active.'));
			} else {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 0));
				$msg = !empty($err) ? $err : 'HTTP ' . $code . ' response from Groq API';
				echo json_encode(array('status' => false, 'message' => 'Groq API connection failed: ' . $msg));
			}
			return;
		}

		if ($service === 'pbx_3cx') {
			$host = trim($row['endpoint']);
			if (empty($host)) {
				echo json_encode(array('status' => false, 'message' => '3CX PBX FQDN/Host URL is empty.'));
				return;
			}
			$parsed = parse_url($host);
			$fqdn = !empty($parsed['host']) ? $parsed['host'] : $host;
			$port = !empty($parsed['port']) ? $parsed['port'] : (!empty($parsed['scheme']) && $parsed['scheme'] === 'https' ? 443 : 5060);

			$fp = @fsockopen($fqdn, $port, $errno, $errstr, 5);
			if ($fp) {
				fclose($fp);
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 1));
				echo json_encode(array('status' => true, 'message' => '3CX PBX server reachable at ' . $fqdn . ':' . $port));
			} else {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 0));
				echo json_encode(array('status' => false, 'message' => 'Could not connect to 3CX host ' . $fqdn . ':' . $port . ' (' . $errstr . ')'));
			}
			return;
		}

		if ($service === 'email_smtp') {
			$host = !empty($config['smtp_host']) ? trim($config['smtp_host']) : '';
			$port = !empty($config['smtp_port']) ? (int)$config['smtp_port'] : 587;
			if (empty($host)) {
				echo json_encode(array('status' => false, 'message' => 'SMTP Host is not configured.'));
				return;
			}
			$fp = @fsockopen($host, $port, $errno, $errstr, 5);
			if ($fp) {
				fclose($fp);
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 1));
				echo json_encode(array('status' => true, 'message' => 'SMTP server successfully reached at ' . $host . ':' . $port));
			} else {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 0));
				echo json_encode(array('status' => false, 'message' => 'Failed to reach SMTP server at ' . $host . ':' . $port . ' (' . $errstr . ')'));
			}
			return;
		}

		if ($service === 'whatsapp') {
			$token = !empty($row['api_key']) ? $row['api_key'] : '';
			if (empty($token)) {
				echo json_encode(array('status' => false, 'message' => 'WhatsApp API token is not configured.'));
				return;
			}
			$endpoint = !empty($row['endpoint']) ? $row['endpoint'] : 'https://graph.facebook.com/v19.0/me';
			$ch = curl_init($endpoint);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
			curl_setopt($ch, CURLOPT_TIMEOUT, 6);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$res = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if ($code >= 200 && $code < 300) {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 1));
				echo json_encode(array('status' => true, 'message' => 'WhatsApp Business Cloud API connected.'));
			} else {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 0));
				echo json_encode(array('status' => false, 'message' => 'WhatsApp verification failed with HTTP ' . $code));
			}
			return;
		}

		if ($service === 'schedule_cron') {
			$cron_url = base_url() . 'cron/daily_schedule_reminders';
			$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 1));
			echo json_encode(array('status' => true, 'message' => 'Internal cron reminder endpoint ready: ' . $cron_url));
			return;
		}

		if ($service === 'helpdesk') {
			$support_email = !empty($config['support_email']) ? $config['support_email'] : '';
			if (!empty($support_email) && filter_var($support_email, FILTER_VALIDATE_EMAIL)) {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 1));
				echo json_encode(array('status' => true, 'message' => 'Helpdesk configuration active with routing to ' . $support_email));
			} else {
				$this->db->where('id', $row['id'])->update('ci_integration_settings', array('status' => 0));
				echo json_encode(array('status' => false, 'message' => 'Please provide a valid support routing email.'));
			}
			return;
		}

		echo json_encode(array('status' => false, 'message' => 'Unsupported service for connection test.'));
	}
}
