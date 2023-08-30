<?php

class Debug {
    public static $data     = [];
    public static $sections = [];

    public static function openSection($section = null) {
        if ($section !== null) {
            self::$sections[$section] = [];
        } else {
            self::$sections[] = [];
        }
    }

    public static function closeSection() {
        $total = count(self::$sections);

        if (!$total) {
            return;
        }

        if ($total > 1) {
            $data            = &self::$sections[Utils::get_last_key(self::$sections, 2)];
            $current_section = array_pop(self::$sections);
            $data[]          = $current_section;
        } else {
            $data            = &self::$data;
            $current_section = Utils::get_last_element(self::$sections, remove:true);
            $key             = key($current_section);
            $data[$key]      = $current_section[$key];
        }
    }

    public static function log($id, $data) {
        if (!defined('_DEBUG')) {
            return;
        }

        $last_section = &self::$data;

        if (self::$sections) {
            $last_section = &self::$sections[Utils::get_last_key(self::$sections)];
        }

        if (!array_key_exists($id, $last_section)) {
            if (is_array($data)) {
                // if (is_numeric(key($data))) {
                //     $data = [...$data];
                // } else {
                //     $data = [$data];
                // }

                $data = is_numeric(key($data)) ? [...$data] : [$data];
            }
            // var_dump($id, $data);

            $last_section[$id] = $data;
            return;
        }

        if (!is_array($last_section[$id])) {
            $last_section[$id] = [$last_section[$id]];
        } else {
            $last_section[$id][] = $data;
        }
    }

    public static function print() {
        echo json_encode(self::$data);
    }
}
