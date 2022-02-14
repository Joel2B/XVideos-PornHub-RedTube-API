<?php

class Cache {
    const CACHE_DIR      = 'cache';
    const CACHE_FILE_EXT = '.json';

    public $cache_file;
    public $expiration_time;
    public $data;

    public function __construct($id, $expiration_time) {
        $path = realpath(dirname(__FILE__)) . '/' . self::CACHE_DIR . '/';

        if (!file_exists($path)) {
            mkdir($path);
        }

        $this->expiration_time = $expiration_time;
        $this->cache_file      = $path . $id . self::CACHE_FILE_EXT;
    }

    public function check() {
        if (!file_exists($this->cache_file)) {
            return false;
        }

        $content   = file_get_contents($this->cache_file);
        $data      = json_decode($content, true);
        $file_time = intval($data['time']);

        $current_time = time();
        $time_left    = $file_time - $current_time;

        if ($time_left > 0) {
            unset($data['time']);

            $this->data = $data;

            return true;
        }

        return false;
    }

    public function save($data) {
        $data['time'] = time() + $this->expiration_time;

        file_put_contents($this->cache_file, json_encode($data, JSON_FORCE_OBJECT));
    }
}
