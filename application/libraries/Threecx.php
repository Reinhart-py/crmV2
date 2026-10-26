<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Threecx
{
	private $config_file;
	private $config = array();

	public function __construct()
	{
		$this->config_file = APPPATH . 'config/3cx_config.json';
		$this->load_config();
	}

	public function load_config()
	{
		$default_config = array(
			'pbx_fqdn' => getenv('THREECX_PBX_FQDN') ? getenv('THREECX_PBX_FQDN') : '',
			'client_id' => getenv('THREECX_CLIENT_ID') ? getenv('THREECX_CLIENT_ID') : '',
			'client_secret' => getenv('THREECX_CLIENT_SECRET') ? getenv('THREECX_CLIENT_SECRET') : '',
			'default_extension' => getenv('THREECX_EXTENSION') ? getenv('THREECX_EXTENSION') : '',
			'multiple_lines' => '',
			'auto_open_webclient' => true,
			'autodialer_seconds' => 60
		);

		if (file_exists($this->config_file)) {
			$raw = @file_get_contents($this->config_file);
			if (!empty($raw)) {
				$decoded = json_decode($raw, true);
				if (is_array($decoded)) {
					$default_config = array_merge($default_config, $decoded);
				}
			}
		}

		$this->config = $default_config;
		return $this->config;
	}

	public function get_config()
	{
		return $this->config;
	}

	public function save_config($data)
	{
		$clean_fqdn = isset($data['pbx_fqdn']) ? trim($data['pbx_fqdn']) : '';
		$clean_fqdn = preg_replace('#^https?://#i', '', $clean_fqdn);
		$clean_fqdn = rtrim($clean_fqdn, '/');
		$seconds = isset($data['autodialer_seconds']) ? (int)$data['autodialer_seconds'] : 60;
		if ($seconds <= 0) {
			$seconds = 60;
		}

		$new_config = array(
			'pbx_fqdn' => $clean_fqdn,
			'client_id' => isset($data['client_id']) ? trim($data['client_id']) : '',
			'client_secret' => isset($data['client_secret']) ? trim($data['client_secret']) : '',
			'default_extension' => isset($data['default_extension']) ? trim($data['default_extension']) : '',
			'multiple_lines' => isset($data['multiple_lines']) ? trim($data['multiple_lines']) : '',
			'auto_open_webclient' => !empty($data['auto_open_webclient']) ? true : false,
			'autodialer_seconds' => $seconds,
			'updated_at' => date('Y-m-d H:i:s')
		);

		$written = @file_put_contents($this->config_file, json_encode($new_config, JSON_PRETTY_PRINT));
		if ($written !== false) {
			$this->config = $new_config;
			return array('status' => true, 'message' => '3CX configuration saved successfully');
		}

		return array('status' => false, 'message' => 'Failed to write 3CX config file');
	}

	public function get_token($fqdn = null, $client_id = null, $client_secret = null)
	{
		$fqdn = $fqdn ? $fqdn : $this->config['pbx_fqdn'];
		$client_id = $client_id ? $client_id : $this->config['client_id'];
		$client_secret = $client_secret ? $client_secret : $this->config['client_secret'];

		$fqdn = preg_replace('#^https?://#i', '', trim($fqdn));
		$fqdn = rtrim($fqdn, '/');

		if (empty($fqdn) || empty($client_id) || empty($client_secret)) {
			return array(
				'status' => false,
				'message' => 'Missing 3CX PBX FQDN, Client ID, or Client Secret. Please configure in settings.'
			);
		}

		$url = 'https://' . $fqdn . '/connect/token';
		$post_fields = http_build_query(array(
			'client_id' => $client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'client_credentials'
		));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/x-www-form-urlencoded',
			'Accept: application/json'
		));

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if (!empty($curl_error)) {
			return array(
				'status' => false,
				'message' => '3CX connection failed: ' . $curl_error
			);
		}

		$data = json_decode($response, true);
		if ($http_code === 200 && !empty($data['access_token'])) {
			return array(
				'status' => true,
				'access_token' => $data['access_token'],
				'token_type' => isset($data['token_type']) ? $data['token_type'] : 'Bearer',
				'expires_in' => isset($data['expires_in']) ? $data['expires_in'] : 3600
			);
		}

		$err_msg = 'Authentication failed (HTTP ' . $http_code . ')';
		if (!empty($data['error_description'])) {
			$err_msg .= ': ' . $data['error_description'];
		} elseif (!empty($data['error'])) {
			$err_msg .= ': ' . $data['error'];
		}

		return array(
			'status' => false,
			'message' => $err_msg,
			'raw_response' => $response
		);
	}

	public function test_connection($fqdn = null, $client_id = null, $client_secret = null)
	{
		$token_res = $this->get_token($fqdn, $client_id, $client_secret);
		if (!$token_res['status']) {
			return $token_res;
		}

		$fqdn = $fqdn ? $fqdn : $this->config['pbx_fqdn'];
		$fqdn = preg_replace('#^https?://#i', '', trim($fqdn));
		$fqdn = rtrim($fqdn, '/');
		$access_token = $token_res['access_token'];

		$url = 'https://' . $fqdn . '/xapi/v1/Defs?$select=Id';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $access_token,
			'Accept: application/json'
		));

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if (!empty($curl_error)) {
			return array(
				'status' => false,
				'message' => 'Verification request failed: ' . $curl_error
			);
		}

		$header_text = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		$version = 'Unknown';
		if (preg_match('/X-3CX-Version:\s*([^\r\n]+)/i', $header_text, $m)) {
			$version = trim($m[1]);
		}

		if ($http_code === 200) {
			return array(
				'status' => true,
				'version' => $version,
				'message' => 'Successfully connected to 3CX PBX (' . $fqdn . ') Version: ' . $version
			);
		}

		return array(
			'status' => false,
			'message' => 'PBX returned HTTP status ' . $http_code . ' during handshake.'
		);
	}

	public function list_users($fqdn = null, $token = null)
	{
		$fqdn = $fqdn ? $fqdn : $this->config['pbx_fqdn'];
		$fqdn = preg_replace('#^https?://#i', '', trim($fqdn));
		$fqdn = rtrim($fqdn, '/');

		if (empty($token)) {
			$token_res = $this->get_token($fqdn);
			if (!$token_res['status']) {
				return array('status' => false, 'users' => array(), 'message' => $token_res['message']);
			}
			$token = $token_res['access_token'];
		}

		$url = 'https://' . $fqdn . '/xapi/v1/Users?$top=100&$select=Id,FirstName,LastName,Number,EmailAddress';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Authorization: Bearer ' . $token,
			'Accept: application/json'
		));

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($http_code === 200) {
			$data = json_decode($response, true);
			$users = isset($data['value']) ? $data['value'] : array();
			return array('status' => true, 'users' => $users);
		}

		return array('status' => false, 'users' => array(), 'message' => 'Failed to fetch users (HTTP ' . $http_code . ')');
	}

	public function build_call_dispatch($phone, $from_extension = null)
	{
		$clean_phone = preg_replace('/[^\d\+]/', '', trim($phone));
		$fqdn = $this->config['pbx_fqdn'];
		$ext = $from_extension ? $from_extension : $this->config['default_extension'];

		$webclient_url = '';
		if (!empty($fqdn)) {
			$webclient_url = 'https://' . $fqdn . '/webclient/#/call?phone=' . urlencode($clean_phone);
		}

		return array(
			'status' => true,
			'phone' => $clean_phone,
			'extension' => $ext,
			'pbx_fqdn' => $fqdn,
			'webclient_url' => $webclient_url,
			'tel_url' => 'tel:' . $clean_phone,
			'app_3cx_url' => '3cx:' . $clean_phone,
			'configured' => (!empty($fqdn) && !empty($this->config['client_id'])) ? true : false
		);
	}
}
