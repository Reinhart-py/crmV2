<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Security extends CI_Security {

    protected $_exclude_uris = array(
        'manager/calls/cdr_webhook',
        'api'
    );

    public function csrf_verify()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        foreach ($this->_exclude_uris as $exclude) {
            if ($exclude !== '' && strpos($uri, $exclude) !== false) {
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
