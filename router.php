<?php
// Router script for PHP built-in webserver (php -S)
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
// Like application/.htaccess on Apache: never serve application/ or system/ files directly
if (preg_match('#^/+(application|system)(/|$)#i', $uri)) { http_response_code(403); exit('Forbidden'); }
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false; // serve the requested resource as-is
}
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/index.php';
