<?php

include 'cache.php';

class Server {
    public $site_info;

    public $cookie_data;
    public $cookie_bypass;
    public $cookie_written = false;

    public $url;
    public $bypass_url;

    public $request;
    public $cookies;

    public $cache;

    public $content = '';

    public function __construct($url, $cookie_data) {
        $this->url         = $url;
        $this->cookie_data = $cookie_data;

        if (!$this->check_data()) {
            return;
        }

        $this->bypass_url = BYPASS_URL . "/?url=$url";

        $this->cookie_data['file'] = $cookie_data['id'] . COOKIE_EXT;

        $expiration_time = $this->site_info['cookie']['expiration_time'];

        $this->cache = new Cache($this->cookie_data['file'], $expiration_time);

        $this->send_request();
    }

    public function check_data() {
        $sites_info = json_decode(file_get_contents(SITES_INFO), true);

        $this->site_info = $sites_info[$this->cookie_data['id']] ?? null;

        if (is_null($this->site_info)) {
            return false;
        }

        if (!preg_match('#' . $this->site_info['url'] . '#', $this->url)) {
            return false;
        }

        if (!ctype_alnum($this->cookie_data['id'])) {
            return false;
        }

        return true;
    }

    public function get_cache_bypass() {
        if (isset($this->cookie_data['bypass'])) {
            return;
        }

        $this->cookie_bypass = file_get_contents($this->bypass_url);

        if ($this->cookie_bypass !== 'null=null') {
            $this->cookie_data['bypass'] = $this->cookie_bypass;
        }
    }

    public function set_cookie_bypass() {
        if (isset($this->cookie_data['bypass'])) {
            $cookie_bypass = explode('=', $this->cookie_data['bypass']);
            $this->request->addCookie($cookie_bypass[0], $cookie_bypass[1]);
        }
    }

    public function read_cookie() {
        if (!$this->cache->check()) {
            return;
        }

        foreach ($this->cache->data as $cookie) {
            $this->request->addCookie($cookie['name'], $cookie['value']);
        }
    }

    public function write_cookie($cookie) {
        $this->cache->save($cookie);
    }

    public function detect_bypass() {
        // for pornhub
        if (!preg_match('/document\.cookie="RNKEY="\+n\+/', $this->content)) {
            return;
        }

        $attempts = 10;

        while ($attempts > 0) {
            $cookie_bypass = file_get_contents($this->bypass_url . '&force=1');

            if ($cookie_bypass !== 'null=null') {
                $this->cookie_data['bypass'] = $cookie_bypass;
                break;
            }

            $attempts--;
        }

        if ($attempts !== 0) {
            $this->send_request();
        }
    }

    public function setup_request() {
        $this->request = new HTTP_Request2();

        $this->request->setUrl($this->url);
        $this->request->setMethod(HTTP_Request2::METHOD_GET);
        $this->request->setConfig(array(
            'follow_redirects' => true,
        ));
    }

    public function send_request() {
        $this->setup_request();

        $this->get_cache_bypass();

        $this->set_cookie_bypass();

        if ($this->cookie_data['action'] === 'read' || $this->cache->check()) {
            $this->read_cookie();
        }

        $response = $this->request->send();

        if ($response->getStatus() !== 200) {
            return;
        }

        $this->content = $response->getBody();

        $this->detect_bypass();

        if ($this->content === '' || $this->cookie_written) {
            return;
        }

        if ($this->cookie_data['action'] === 'write' && !$this->cache->check()) {
            $this->write_cookie($response->getCookies());
            $this->cookie_written = true;
        }
    }

    public function get_content() {
        return $this->content;
    }
}
