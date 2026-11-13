<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Server command line only (php index.php cli ...). Used by the installer when it sets up a client's own Sales CRM.
 *
 *   php index.php cli setup_admin <email> "<Full name>"
 *     Creates the first user: an administrator with full access, signing in with <email>.
 *     Works only while the CRM has no users at all. The account has no usable password; the command
 *     prints a one-time link (valid 72 hours) where the client sets it. Needs BASE_URL in .env.
 *     Exit code 0 when done, 1 when nothing was created (the reason is printed).
 */
class Cli extends CI_Controller
{
	const LINK_HOURS = 72;

	public function __construct()
	{
		parent::__construct();
		if (!$this->input->is_cli_request()) {
			show_404();
		}
		$this->load->database();
	}

	public function index()
	{
		$this->out("Commands:\n  php index.php cli setup_admin <email> \"<Full name>\"");
	}

	public function setup_admin($email = '', $name = '')
	{
		// CodeIgniter splits arguments on spaces; the rest of the words belong to the name.
		$args = func_get_args();
		$email = strtolower(trim((string)$email));
		$name = trim(implode(' ', array_slice($args, 1)));
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->fail('Give the administrator\'s email: php index.php cli setup_admin <email> "<Full name>"');
		if ($name === '') $name = $email;
		$name = mb_substr($name, 0, 100);
		$base = rtrim((string)getenv('BASE_URL'), '/');
		if (!preg_match('~^https?://~i', $base)) $this->fail('BASE_URL is not set in .env, so the set-password link cannot be made.');

		if (!$this->db->table_exists('ci_user') || !$this->db->table_exists('ci_roles')) $this->fail('The database is not set up yet (load database/clean.sql and run php migration_runner.php first).');
		if ($this->db->count_all('ci_user') > 0) $this->fail('This Sales CRM already has users, so no first administrator is created. Add people from Staff instead.');
		$role = $this->db->select('id')->where('role_type', 'admin')->where('status', 1)->order_by('id', 'ASC')->get('ci_roles')->row_array();
		if (!$role) $this->fail('The Admin role is missing from ci_roles.');

		$token = bin2hex(random_bytes(24)) . '_' . (time() + self::LINK_HOURS * 3600);
		$row = array(
			'name' => $name, 'email' => $email, 'c_email' => $email, 'c_username' => $email,
			'c_password' => sha1(bin2hex(random_bytes(24))), 'fotp' => $token,
			'mobile' => '', 'country_code' => '', 'country_code2' => '', 'address' => '', 'c_mobile' => '', 'c_salary' => '',
			'c_doj' => '', 'c_dol' => '', 'national' => '', 'p_no' => '', 'p_date' => '', 'p_exdate' => '', 'emp_id' => '', 'department' => '',
			'status' => 1, 'admin_status' => 1, 'user_type' => (int)$role['id'], 'manager' => 0, 'user_level' => 1, 'created' => date('Y-m-d'),
		);
		$this->db->trans_start();
		if ($this->db->count_all('ci_user') > 0) { $this->db->trans_complete(); $this->fail('This Sales CRM already has users.'); }
		$this->db->insert('ci_user', $row);
		$uid = (int)$this->db->insert_id();
		$this->db->trans_complete();
		if ($uid <= 0 || !$this->db->trans_status()) $this->fail('The administrator could not be saved: ' . $this->db->_error_message());

		if ($this->db->table_exists('ci_admin_logs')) {
			// Written directly: the usual log helper reads the browser session, which the command line does not have.
			$this->db->insert('ci_admin_logs', array(
				'user_id' => $uid, 'username' => $name, 'ip_address' => 'server', 'user_agent' => 'command line',
				'activity' => 'user_created', 'status' => 'SUCCESS',
				'details' => 'First administrator created from the server command line: ' . $email, 'created_at' => date('Y-m-d H:i:s'),
			));
		}
		$this->out('First administrator created: ' . $name . ' <' . $email . '> (user #' . $uid . ', full access).');
		$this->out('Send this link to them to set their password (valid ' . self::LINK_HOURS . ' hours, once):');
		$this->out($base . '/manager/bnr54dfdf/' . $token);
		$this->out('After it expires they can use "Forgot Password?" on the sign-in page.');
		exit(0);
	}

	private function out($s)
	{
		echo $s, PHP_EOL;
	}

	private function fail($msg)
	{
		fwrite(STDERR, $msg . PHP_EOL);
		exit(1);
	}
}
