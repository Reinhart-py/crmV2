<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';
$active_record = TRUE;
 
if (!function_exists('crm_load_env')) {
	function crm_load_env($path) {
		if (!file_exists($path) || !is_readable($path)) {
			return;
		}
		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			$line = trim($line);
			if ($line === '' || $line[0] === '#') {
				continue;
			}
			if (strpos($line, '=') !== false) {
				list($key, $val) = explode('=', $line, 2);
				$key = trim($key);
				$val = trim($val);
				if (strlen($val) > 1 && (($val[0] === '"' && $val[-1] === '"') || ($val[0] === "'" && $val[-1] === "'"))) {
					$val = substr($val, 1, -1);
				}
				if (!array_key_exists($key, $_SERVER) && !array_key_exists($key, $_ENV)) {
					putenv("$key=$val");
					$_ENV[$key] = $val;
					$_SERVER[$key] = $val;
				}
			}
		}
	}
}

crm_load_env(defined('FCPATH') ? FCPATH . '.env' : __DIR__ . '/../../.env');

$is_local = (
    (isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) ||
    (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== FALSE || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== FALSE)) ||
    (php_sapi_name() === 'cli')
);

$env_host = getenv('DB_HOST') ? getenv('DB_HOST') : (getenv('DB_HOSTNAME') ? getenv('DB_HOSTNAME') : '127.0.0.1');
$env_user = getenv('DB_USER') ? getenv('DB_USER') : (getenv('DB_USERNAME') ? getenv('DB_USERNAME') : ($is_local ? 'root' : 'crm7strokes'));
$env_pass = getenv('DB_PASS') ? getenv('DB_PASS') : (getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '');
$env_name = getenv('DB_NAME') ? getenv('DB_NAME') : (getenv('DB_DATABASE') ? getenv('DB_DATABASE') : 'crm7strokes');

$db['default']['hostname'] = $env_host;
$db['default']['username'] = $env_user;
$db['default']['password'] = $env_pass;
$db['default']['database'] = $env_name;
$db['default']['dbdriver'] = 'mysqli';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */