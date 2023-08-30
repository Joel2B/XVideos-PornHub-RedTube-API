<?php

include 'user_agent.php';

class Utils {
    public static function get_url_content($url, $bypass = false) {
        $user_agent = \Campo\UserAgent::random(array('device_type' => 'Desktop'));
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? $user_agent;

        $headers = ['Cache-Control: no-cache'];

        $curl = curl_init();

        $options = [
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => $user_agent,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_FRESH_CONNECT  => false,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => TIMEOUT,
            CURLOPT_RETURNTRANSFER => true,
        ];

        if ($bypass) {
            $options[CURLOPT_COOKIE] = self::get_url_content(BYPASS_URL . '/?url=' . $url);
        }

        curl_setopt_array($curl, $options);

        $content = curl_exec($curl);

        curl_close($curl);

        return $content;
    }

    public static function get_redirect_url($url) {
        $ch = curl_init($url);

        $options = [
            CURLOPT_HEADER         => false,
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => TIMEOUT,
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $redirect_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

        curl_close($ch);

        return $redirect_url;
    }

    public static function get_multiple_urls($urls) {
        $user_agent = \Campo\UserAgent::random(array('device_type' => 'Desktop'));
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? $user_agent;

        $curl   = [];
        $result = [];

        $mh = curl_multi_init();

        $headers = ['Cache-Control: no-cache'];

        foreach ($urls as $id => $data) {
            $url       = $data['url'];
            $curl[$id] = curl_init($url);

            $options = [
                CURLOPT_USERAGENT      => $user_agent,
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_FRESH_CONNECT  => false,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
                CURLOPT_TIMEOUT        => TIMEOUT,
                CURLOPT_RETURNTRANSFER => true,
            ];

            if ($data['bypass']) {
                $cookie = self::get_url_content(BYPASS_URL . '/?url=' . $url);

                if (!empty($cookie)) {
                    $options[CURLOPT_COOKIE] = $cookie;
                }
            }

            curl_setopt_array($curl[$id], $options);
            curl_multi_add_handle($mh, $curl[$id]);
        }

        $running = null;

        do {
            curl_multi_exec($mh, $running);
        } while ($running);

        foreach ($urls as $id => $data) {
            $result[$id] = [
                'url'     => $data['url'],
                'content' => curl_multi_getcontent($curl[$id]),
            ];

            curl_multi_remove_handle($mh, $curl[$id]);
        }

        curl_multi_close($mh);

        return $result;
    }

    public static function get_http_code($url) {
        $ch = curl_init($url);

        $options = [
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => TIMEOUT,
        ];

        curl_setopt_array($ch, $options);
        curl_exec($ch);

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $http_code;
    }

    public static function _match($regex, $content) {
        preg_match('#' . $regex . '#', $content, $match);
        return $match[1] ?? null;
    }

    public static function clear_url($url) {
        return str_replace('\/', '/', $url);
    }

    public static function is_url($url) {
        return preg_match("{^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:/?#\[\]@!\$&'\(\)\*\+,;=.]+}", $url);
    }

    public static function add_missing_data($source, $data, &$missing_data) {
        if ($source === '') {
            if (!in_array($data, $missing_data)) {
                $missing_data[] = $data;
            }
        }
    }

    public static function remove_missing_data($data, &$missing_data) {
        $key = array_search($data, $missing_data);
        if ($key !== false) {
            unset($missing_data[$key]);
        }
    }

    public static function in_array_any($needles, $haystack) {
        return !empty(array_intersect($needles, $haystack));
    }

    public static function get_deeper_keys($from, &$to, $withValues = false) {
        foreach ($from as $key => $value) {
            if (is_array($value)) {
                self::get_deeper_keys($value, $to, $withValues);
            } else {
                if ($withValues) {
                    $to[$key] = $value;
                } else {
                    $to[] = $key;
                }
            }
        }
    }

    public static function remove_parent_array($from, &$to, $parent) {
        foreach ($from as $key => $value) {
            if ($key === $parent) {
                if (is_array($value)) {
                    unset($to[$key]);

                    foreach ($value as $key => $value) {
                        $to[$key] = $value;
                    }
                }
            } else if (is_array($value)) {
                $to[$key] = [];
                self::remove_parent_array($value, $to[$key], $parent);
            } else {
                $to[$key] = $value;
            }
        }
    }

    public static function remove_multiple_parent_array(&$from) {
        $num_args = func_num_args();
        $args     = func_get_args();

        for ($i = 1; $i < $num_args; $i++) {
            $to = [];
            self::remove_parent_array($from, $to, $args[$i]);
            $from = $to;
        }
    }

    public static function is_empty_array($arr) {
        $empty = true;

        foreach ($arr as $value) {
            if (is_array($value)) {
                $empty = self::is_empty_array($value);
            } else {
                if (!empty($value)) {
                    return false;
                }
            }
        }

        return $empty;
    }

    public static function get_last_key($array, $n = 1) {
        return key(self::get_last_element($array, $n));
    }

    public static function get_last_element(&$array, $n = 1, $remove = false) {
        $extract = array_slice($array, -$n, 1, true);

        if ($remove) {
            array_pop($array);
        }

        return $extract;
    }
}
