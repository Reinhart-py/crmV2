<?php
class Mail_model extends CI_Model {

	/** Last send error (empty after a successful send). */
	public $last_error = '';

	function __construct()
	{
		parent::__construct();
	}

	/**
	 * SMTP settings used for every outgoing email.
	 *
	 * Order: 1) System > Integrations > Email SMTP (when enabled and a host is set),
	 * 2) SMTP_* values in .env, 3) the legacy built-in mailbox (kept so nothing
	 * stops working on servers that were never configured).
	 */
	public function smtp_config()
	{
		$cfg = array(
			'host' => 'storeyourcode.com',
			'port' => 25,
			'crypto' => 'tls',
			'user' => 'no-reply@storeyourcode.com',
			'pass' => 'noreply@123#',
			'from_email' => 'no-reply@storeyourcode.com',
			'from_name' => 'Sales CRM',
			'source' => 'built-in default',
		);

		if (getenv('SMTP_HOST')) {
			$cfg = array(
				'host' => getenv('SMTP_HOST'),
				'port' => (int)(getenv('SMTP_PORT') ?: 587),
				'crypto' => getenv('SMTP_CRYPTO') ?: 'tls',
				'user' => (string)getenv('SMTP_USER'),
				'pass' => (string)getenv('SMTP_PASS'),
				'from_email' => getenv('SMTP_FROM_EMAIL') ?: (string)getenv('SMTP_USER'),
				'from_name' => getenv('SMTP_FROM_NAME') ?: 'Sales CRM',
				'source' => '.env',
			);
		}

		if ($this->db->table_exists('ci_integration_settings')) {
			$row = $this->db->where('service_name', 'email_smtp')->get('ci_integration_settings')->row_array();
			$c = !empty($row['config_json']) ? json_decode($row['config_json'], true) : array();
			if (!empty($row) && (int)$row['status'] === 1 && is_array($c) && !empty($c['smtp_host'])) {
				$cfg = array(
					'host' => trim($c['smtp_host']),
					'port' => !empty($c['smtp_port']) ? (int)$c['smtp_port'] : 587,
					'crypto' => !empty($c['smtp_crypto']) ? $c['smtp_crypto'] : 'tls',
					'user' => isset($c['smtp_user']) ? trim($c['smtp_user']) : '',
					'pass' => isset($c['smtp_pass']) ? $c['smtp_pass'] : '',
					'from_email' => !empty($c['from_email']) ? trim($c['from_email']) : (isset($c['smtp_user']) ? trim($c['smtp_user']) : ''),
					'from_name' => !empty($c['from_name']) ? trim($c['from_name']) : 'Sales CRM',
					'source' => 'System > Integrations',
				);
			}
		}
		return $cfg;
	}

	/**
	 * Send an HTML email. Returns "ok" on success, otherwise "Mailer Error: ...".
	 * Failures are also written to application/logs/mail-YYYY-MM-DD.log.
	 */
	public function sendmail($to = '', $subject = '', $data = array(), $message = '', $to_name = '')
	{
		$this->last_error = '';
		$cfg = $this->smtp_config();

		// Logo travels inside the email (cid:), so it shows even where the CRM address is not reachable.
		$logo_file = FCPATH . 'assets/email-logo.png';
		$logo = 'cid:crm-logo';
		$full_msg = '<table cellpadding="0" cellspacing="0" width="100%" style="background:#f1f5f9;padding:24px 0;font-family:Arial,Helvetica,sans-serif;"><tr><td align="center">'
			. '<table cellpadding="0" cellspacing="0" width="560" style="background:#ffffff;border-radius:8px;border:1px solid #e2e8f0;">'
			. '<tr><td style="padding:24px 28px 8px;"><img src="' . $logo . '" alt="Seven Strokes" width="80" height="56" style="display:block;border:0;"></td></tr>'
			. '<tr><td style="padding:8px 28px 24px;color:#0f172a;font-size:15px;line-height:1.6;">' . $message . '</td></tr>'
			. '<tr><td style="padding:14px 28px;border-top:1px solid #e2e8f0;color:#64748b;font-size:12px;">Sales CRM. This is an automated message, please do not reply.</td></tr>'
			. '</table></td></tr></table>';

		require_once(APPPATH . 'libraries/phpmailer/PHPMailerAutoload.php');
		$mail = new PHPMailer;
		$mail->isSMTP();
		$mail->SMTPDebug = 0;
		$mail->CharSet = 'UTF-8';
		$mail->Timeout = 20;
		$mail->Host = $cfg['host'];
		$mail->Port = (int)$cfg['port'];

		$crypto = strtolower((string)$cfg['crypto']);
		if ($crypto === 'ssl' || $crypto === 'tls') {
			$mail->SMTPSecure = $crypto;
		} else {
			$mail->SMTPSecure = '';
			$mail->SMTPAutoTLS = false;
		}
		$mail->SMTPAuth = ($cfg['user'] !== '');
		$mail->Username = $cfg['user'];
		$mail->Password = $cfg['pass'];

		$from = filter_var($cfg['from_email'], FILTER_VALIDATE_EMAIL) ? $cfg['from_email'] : $cfg['user'];
		$mail->setFrom($from, $cfg['from_name']);
		// Message-ID and SMTP greeting use the sending domain, not "localhost"; no mailer advert header.
		$domain = substr(strrchr($from, '@'), 1);
		if ($domain) $mail->Hostname = $domain;
		$mail->XMailer = ' ';
		$mail->Sender = $from;
		$mail->addAddress($to, $to_name);
		if (!empty($data['reply_to'][0]) && filter_var($data['reply_to'][0], FILTER_VALIDATE_EMAIL)) {
			$mail->addReplyTo($data['reply_to'][0], isset($data['reply_to'][1]) ? $data['reply_to'][1] : '');
		}
		$mail->Subject = $subject;
		$mail->msgHTML($full_msg);
		if (is_file($logo_file)) {
			$mail->addStringEmbeddedImage(file_get_contents($logo_file), 'crm-logo', 'seven-strokes.png', 'base64', 'image/png');
		}
		$mail->AltBody = trim(html_entity_decode(strip_tags(str_replace(array('<br>', '<br/>', '<br />', '</p>'), "\n", $message))));

		try {
			$ok = $mail->send();
		} catch (Exception $e) {
			$ok = false;
			$mail->ErrorInfo = $e->getMessage();
		}

		if (!$ok) {
			$this->last_error = $mail->ErrorInfo;
			$this->write_log('FAILED to=' . $to . ' via=' . $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['source'] . ') subject="' . $subject . '" error=' . $mail->ErrorInfo);
			return 'Mailer Error: ' . $mail->ErrorInfo;
		}
		$this->write_log('SENT to=' . $to . ' via=' . $cfg['host'] . ':' . $cfg['port'] . ' (' . $cfg['source'] . ') subject="' . $subject . '"');
		return 'ok';
	}

	private function write_log($line)
	{
		$dir = APPPATH . 'logs/';
		if (is_dir($dir) && is_writable($dir)) {
			@file_put_contents($dir . 'mail-' . date('Y-m-d') . '.log', date('Y-m-d H:i:s') . ' ' . $line . "\n", FILE_APPEND);
		}
	}

	public function load_mailer()
	{
		require_once(APPPATH . 'libraries/phpmailer/PHPMailerAutoload.php');
	}
}
