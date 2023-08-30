<?php

if (count(get_included_files()) === 1) {
    die();
}

class Performance {
    public static $data;
    public static $timestamp;

    public static function now($id) {
        if (!defined('_DEBUG')) {
            return;
        }

        if (!isset(self::$data[$id])) {
            self::$data[$id] = microtime(true);
        } else {
            $time = number_format(microtime(true) - self::$data[$id], 2);
            
            unset(self::$data[$id]);

            return [$id => $time];
        }
    }
}
