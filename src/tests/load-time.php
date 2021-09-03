<?php

if (count(get_included_files()) == 1) {
    die();
}

class LoadTime {
    public static $data;
    public static $start;

    public static function start($id) {
        if (!defined('DEBUG') || DEBUG == false) {
            return;
        }
        self::$data[$id] = self::$start = microtime(true);
    }

    public static function end($id) {
        if (!defined('DEBUG') || DEBUG == false) {
            return;
        }
        $time_elapsed = number_format(microtime(true) - self::$data[$id], 2);
        echo "<b>{$time_elapsed}s $id</b><hr>";
    }
}
