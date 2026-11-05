<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sessions are kept on the server (table ci_sessions); the browser cookie only carries a random
 * session id, so who is signed in and their role cannot be forged by editing the cookie.
 * The table is created automatically the first time it is needed.
 */
class MY_Session extends CI_Session {

	public function __construct($params = array())
	{
		$CI =& get_instance();
		if (!isset($CI->db) || !is_object($CI->db)) {
			$CI->load->database();
		}
		$CI->db->query("CREATE TABLE IF NOT EXISTS ci_sessions (
			session_id VARCHAR(40) NOT NULL DEFAULT '0',
			ip_address VARCHAR(45) NOT NULL DEFAULT '0',
			user_agent VARCHAR(120) NOT NULL,
			last_activity INT(10) UNSIGNED NOT NULL DEFAULT 0,
			user_data MEDIUMTEXT NOT NULL,
			PRIMARY KEY (session_id),
			KEY last_activity_idx (last_activity)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8");
		parent::__construct($params);
	}
}
