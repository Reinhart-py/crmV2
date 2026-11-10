<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security {

    // only calls that come from outside services (they cannot carry the CRM form token);
    // everything else, including api/whatsapp/send, needs the token
    protected $_exclude_uris = array(
        'manager/calls/cdr_webhook',
        'api/calls/recording_webhook',
        'api/calls/cdr_webhook',
        'api/calls/process_ai',
        'api/whatsapp/webhook',
        'api/ai-calls/webhook',
        'api/ai-calls/cron',
        'api/marketing/meta',
        'api/marketing/lead',
        'api/marketing/cron',
        'api/subscription/events',
        'api/suite/app-events'
    );

    public function csrf_verify()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? (string)parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
        $uri = '/' . trim($uri, '/');
        foreach ($this->_exclude_uris as $exclude) {
            if ($exclude !== '' && strpos($uri, '/' . $exclude) !== false) {
                return $this;
            }
        }

        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST')
        {
            return $this->csrf_set_cookie();
        }

        $token = isset($_POST[$this->_csrf_token_name]) ? $_POST[$this->_csrf_token_name] : null;
        if (!$token) {
            $token = isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? $_SERVER['HTTP_X_CSRF_TOKEN'] : (isset($_SERVER['HTTP_X_XSRF_TOKEN']) ? $_SERVER['HTTP_X_XSRF_TOKEN'] : null);
        }

        $cookie = isset($_COOKIE[$this->_csrf_cookie_name]) ? $_COOKIE[$this->_csrf_cookie_name] : null;

        if (!$token || !$cookie || $token !== $cookie)
        {
            $this->csrf_show_error();
        }

        if (isset($_POST[$this->_csrf_token_name])) {
            unset($_POST[$this->_csrf_token_name]);
        }

        return $this;
    }
}
