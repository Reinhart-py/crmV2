<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Groq_service {

	private $api_key;
	private $base_url = 'https://api.groq.com/openai/v1';
	private $transcription_model;
	private $summary_model;
	private $fallback_summary_model = 'openai/gpt-oss-120b';
	private $ci;

	public function __construct()
	{
		$this->ci =& get_instance();
		$this->ci->load->database();

		$setting_row = $this->ci->db->get_where('ci_integration_settings', array('service_name' => 'groq'))->row_array();
		$cfg = !empty($setting_row['config_json']) ? json_decode($setting_row['config_json'], true) : array();

		$env_key = getenv('GROQ_API_KEY');
		if (!empty($env_key)) {
			$this->api_key = $env_key;
		} elseif (!empty($setting_row['api_key'])) {
			$this->api_key = $setting_row['api_key'];
		} else {
			$this->api_key = defined('GROQ_API_KEY') ? GROQ_API_KEY : '';
		}

		$env_trans_model = getenv('GROQ_TRANSCRIPTION_MODEL');
		if (!empty($env_trans_model)) {
			$this->transcription_model = $env_trans_model;
		} elseif (!empty($cfg['transcription_model'])) {
			$this->transcription_model = $cfg['transcription_model'];
		} else {
			$this->transcription_model = 'whisper-large-v3-turbo';
		}

		$env_chat_model = getenv('GROQ_CHAT_MODEL');
		$env_summary_model = getenv('GROQ_SUMMARY_MODEL');
		if (!empty($env_chat_model)) {
			$this->summary_model = $env_chat_model;
		} elseif (!empty($env_summary_model)) {
			$this->summary_model = $env_summary_model;
		} elseif (!empty($cfg['summary_model'])) {
			$this->summary_model = $cfg['summary_model'];
		} else {
			$this->summary_model = 'openai/gpt-oss-20b';
		}

		if (!empty($cfg['fallback_summary_model'])) {
			$this->fallback_summary_model = $cfg['fallback_summary_model'];
		}
	}

	public function get_api_key()
	{
		return $this->api_key;
	}

	public function set_api_key($key)
	{
		$this->api_key = trim((string)$key);
	}

	public function is_configured()
	{
		return !empty($this->api_key);
	}

	public function transcribe_audio($file_path)
	{
		if (!$this->is_configured()) {
			return array(
				'status' => false,
				'message' => 'GROQ_API_KEY is not configured in the environment or settings.'
			);
		}

		if (!file_exists($file_path) || filesize($file_path) === 0) {
			return array(
				'status' => false,
				'message' => 'Audio file does not exist or is empty: ' . $file_path
			);
		}

		$url = $this->base_url . '/audio/transcriptions';
		$cfile = new CURLFile(realpath($file_path), mime_content_type($file_path) ?: 'audio/mpeg', basename($file_path));

		$post_data = array(
			'file' => $cfile,
			'model' => $this->transcription_model,
			'response_format' => 'json'
		);

		$headers = array(
			'Authorization: Bearer ' . $this->api_key
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 180);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);

		if (!empty($err)) {
			return array('status' => false, 'message' => 'CURL error during transcription: ' . $err);
		}

		$res_arr = json_decode($response, true);
		if ($http_code >= 200 && $http_code < 300 && !empty($res_arr['text'])) {
			return array(
				'status' => true,
				'transcript' => trim($res_arr['text']),
				'model' => $this->transcription_model
			);
		}

		$error_msg = isset($res_arr['error']['message']) ? $res_arr['error']['message'] : ('HTTP ' . $http_code . ': ' . $response);
		return array('status' => false, 'message' => $error_msg, 'http_code' => $http_code);
	}

	public function analyze_transcript($transcript, $model_override = null, $is_retry = false)
	{
		if (!$this->is_configured()) {
			return array(
				'status' => false,
				'message' => 'GROQ_API_KEY is not configured.'
			);
		}

		if (empty($transcript)) {
			return array(
				'status' => false,
				'message' => 'Transcript is empty. Cannot analyze conversation.'
			);
		}

		$active_model = $model_override ?: $this->summary_model;

		$prompt = "You are a sales-call analyst for a CRM. Analyze the following call transcript and return ONLY valid JSON. Do not add explanations, markdown, or extra text.\n\n" .
			"Return this JSON structure:\n" .
			"{\n" .
			"  \"summary\": \"2-4 line concise summary of the conversation\",\n" .
			"  \"remarks\": \"Detailed but concise remarks covering customer requirement, objections, budget, timeline, competitor mentions, and important commitments\",\n" .
			"  \"next_status\": \"Interested | Not Interested | Follow-up Required | Callback Scheduled | Wrong Number | No Response | Not Reachable | Deal Closed | Requirement Not Clear\",\n" .
			"  \"sentiment\": \"Positive | Neutral | Negative\",\n" .
			"  \"follow_up_date\": \"YYYY-MM-DD or null\",\n" .
			"  \"next_action\": \"Exact next action for the sales agent\",\n" .
			"  \"language\": \"Primary call language, for example English, Hindi, Hinglish\",\n" .
			"  \"customer_requirements\": [\"Short requirement 1\", \"Short requirement 2\"],\n" .
			"  \"objections\": [\"Objection 1\", \"Objection 2\"]\n" .
			"}\n\n" .
			"Rules:\n" .
			"- Base every field only on the transcript.\n" .
			"- If information is not available, use null or an empty array as appropriate for the field.\n" .
			"- Do not invent commitments, dates, budgets, or customer requirements.\n" .
			"- If the call is mostly Hindi or Hinglish, write the summary, remarks, and next action in simple Hinglish/English suitable for a sales agent.\n" .
			"- next_status must be one of the allowed values only.\n\n" .
			"Call Transcript:\n" . $transcript;

		$payload = array(
			'model' => $active_model,
			'messages' => array(
				array('role' => 'user', 'content' => $prompt)
			),
			'temperature' => 0,
			'response_format' => array('type' => 'json_object')
		);

		$url = $this->base_url . '/chat/completions';
		$headers = array(
			'Authorization: Bearer ' . $this->api_key,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);

		if (!empty($err)) {
			return array('status' => false, 'message' => 'CURL error during analysis: ' . $err);
		}

		$res_arr = json_decode($response, true);

		if ($http_code === 404 || (isset($res_arr['error']['code']) && $res_arr['error']['code'] === 'model_not_found') || (isset($res_arr['error']['message']) && stripos($res_arr['error']['message'], 'model') !== false && stripos($res_arr['error']['message'], 'not found') !== false)) {
			if ($active_model !== $this->fallback_summary_model) {
				return $this->analyze_transcript($transcript, $this->fallback_summary_model, false);
			}
		}

		if ($http_code >= 200 && $http_code < 300 && !empty($res_arr['choices'][0]['message']['content'])) {
			$raw_content = trim($res_arr['choices'][0]['message']['content']);
			$clean_content = preg_replace('/^```(?:json)?\s*/i', '', $raw_content);
			$clean_content = preg_replace('/\s*```$/', '', $clean_content);
			$parsed = json_decode($clean_content, true);

			if (empty($parsed) || !is_array($parsed)) {
				if (!$is_retry) {
					return $this->analyze_transcript($transcript, $active_model, true);
				}
				return array(
					'status' => false,
					'message' => 'Failed to parse JSON response from Groq.',
					'raw' => $raw_content
				);
			}

			$validated = $this->validate_ai_schema($parsed);
			return array(
				'status' => true,
				'model' => $active_model,
				'data' => $validated,
				'raw_json' => $raw_content
			);
		}

		$error_msg = isset($res_arr['error']['message']) ? $res_arr['error']['message'] : ('HTTP ' . $http_code . ': ' . $response);
		return array('status' => false, 'message' => $error_msg, 'http_code' => $http_code);
	}

	private function validate_ai_schema($parsed)
	{
		$allowed_statuses = array(
			'Interested',
			'Not Interested',
			'Follow-up Required',
			'Callback Scheduled',
			'Wrong Number',
			'No Response',
			'Not Reachable',
			'Deal Closed',
			'Requirement Not Clear'
		);

		$summary = isset($parsed['summary']) ? trim((string)$parsed['summary']) : '';
		$remarks = isset($parsed['remarks']) ? trim((string)$parsed['remarks']) : '';
		$next_status = isset($parsed['next_status']) ? trim((string)$parsed['next_status']) : 'Follow-up Required';

		$matched_status = 'Follow-up Required';
		foreach ($allowed_statuses as $st) {
			if (strcasecmp($st, $next_status) === 0) {
				$matched_status = $st;
				break;
			}
		}

		$sentiment = isset($parsed['sentiment']) ? trim((string)$parsed['sentiment']) : 'Neutral';
		if (!in_array(ucfirst(strtolower($sentiment)), array('Positive', 'Neutral', 'Negative'))) {
			$sentiment = 'Neutral';
		} else {
			$sentiment = ucfirst(strtolower($sentiment));
		}

		$follow_up_date = null;
		if (!empty($parsed['follow_up_date']) && strtolower($parsed['follow_up_date']) !== 'null') {
			$d_str = trim((string)$parsed['follow_up_date']);
			if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $d_str)) {
				$follow_up_date = $d_str;
			}
		}

		$next_action = isset($parsed['next_action']) ? trim((string)$parsed['next_action']) : '';
		$language = isset($parsed['language']) ? trim((string)$parsed['language']) : 'English';

		$cust_reqs = array();
		if (!empty($parsed['customer_requirements']) && is_array($parsed['customer_requirements'])) {
			foreach ($parsed['customer_requirements'] as $req) {
				$r_clean = trim((string)$req);
				if (!empty($r_clean)) {
					$cust_reqs[] = $r_clean;
				}
			}
		}

		$objections = array();
		if (!empty($parsed['objections']) && is_array($parsed['objections'])) {
			foreach ($parsed['objections'] as $obj) {
				$o_clean = trim((string)$obj);
				if (!empty($o_clean)) {
					$objections[] = $o_clean;
				}
			}
		}

		return array(
			'summary' => $summary,
			'remarks' => $remarks,
			'next_status' => $matched_status,
			'sentiment' => $sentiment,
			'follow_up_date' => $follow_up_date,
			'next_action' => $next_action,
			'language' => $language,
			'customer_requirements' => $cust_reqs,
			'objections' => $objections
		);
	}

	public function download_recording($remote_url, $call_id = null)
	{
		if (empty($remote_url)) {
			return array('status' => false, 'message' => 'Empty remote recording URL');
		}

		$parsed = parse_url($remote_url);
		$scheme = isset($parsed['scheme']) ? strtolower($parsed['scheme']) : '';
		if ($scheme !== 'https' && $scheme !== 'http') {
			if (file_exists($remote_url)) {
				return array('status' => true, 'local_path' => $remote_url);
			}
			return array('status' => false, 'message' => 'Invalid recording URL scheme: ' . $scheme);
		}

		$ext = pathinfo($parsed['path'] ?? '', PATHINFO_EXTENSION);
		if (empty($ext) || !in_array(strtolower($ext), array('wav', 'mp3', 'm4a', 'ogg', 'webm'))) {
			$ext = 'wav';
		}

		$filename = 'rec_' . ($call_id ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $call_id) : uniqid()) . '_' . time() . '.' . $ext;
		$target_dir = FCPATH . 'assets/uploads/recordings/';
		if (!is_dir($target_dir)) {
			mkdir($target_dir, 0777, true);
		}
		$target_path = $target_dir . $filename;

		$fp = fopen($target_path, 'w+');
		if (!$fp) {
			return array('status' => false, 'message' => 'Failed to create recording file handle');
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $remote_url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$token = $this->ci->threecx->get_token();
		if (!empty($token['access_token'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token['access_token']));
		}

		curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$err = curl_error($ch);
		curl_close($ch);
		fclose($fp);

		if (!empty($err) || $http_code >= 400 || filesize($target_path) === 0) {
			@unlink($target_path);
			return array('status' => false, 'message' => 'Failed downloading recording. HTTP ' . $http_code . ' Error: ' . $err);
		}

		$rel_path = 'assets/uploads/recordings/' . $filename;
		return array('status' => true, 'local_path' => $target_path, 'relative_path' => $rel_path);
	}
}
