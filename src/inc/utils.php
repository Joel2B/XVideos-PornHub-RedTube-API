<?php

// TODO: user agent must be made random
class Utils {
    public static function get_url_content($url, $cookie = false) {
        $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Safari/537.36';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? $user_agent;
        $headers    = ['Cache-Control: no-cache'];
        $curl       = curl_init();
        $options    = [
            CURLOPT_URL            => $url,
            CURLOPT_USERAGENT      => $user_agent,
            CURLOPT_AUTOREFERER    => true,
            CURLOPT_FRESH_CONNECT  => true,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => TIMEOUT,
            CURLOPT_RETURNTRANSFER => true
            // CURLOPT_SSL_VERIFYPEER => false
        ];
        if ($cookie) {
            $options[CURLOPT_COOKIEFILE] = dirname(__FILE__) . '\tmp\cookie.txt';
        }
        curl_setopt_array($curl, $options);
        $content = curl_exec($curl);
        curl_close($curl);
        return $content;
    }

    public static function get_redirect_url($url) {
        $ch      = curl_init($url);
        $options = [
            CURLOPT_HEADER         => false,
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => TIMEOUT
        ];
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        $redirect_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $redirect_url;
    }

    public static function get_multiple_urls(
        $urls,
        $cookie = false,
        $http_code = false
    ) {
        $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Safari/537.36';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? $user_agent;

        $curl   = [];
        $result = [];

        $mh = curl_multi_init();

        $headers = ['Cache-Control: no-cache'];

        foreach ($urls as $id => $link) {
            $curl[$id] = curl_init($link);

            $options = [
                CURLOPT_USERAGENT      => $user_agent,
                CURLOPT_AUTOREFERER    => true,
                CURLOPT_FRESH_CONNECT  => true,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS      => 5,
                CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
                CURLOPT_TIMEOUT        => TIMEOUT,
                CURLOPT_RETURNTRANSFER => true
                // CURLOPT_SSL_VERIFYPEER => false
            ];

            if ($cookie) {
                $options[CURLOPT_COOKIEJAR] = dirname(__FILE__) . '\tmp\cookie.txt';
            }

            if ($http_code) {
                $options[CURLOPT_HEADER] = 1;
                $options[CURLOPT_NOBODY] = 1;
            }

            curl_setopt_array($curl[$id], $options);
            curl_multi_add_handle($mh, $curl[$id]);
        }

        $running = null;
        LoadTime::start('gmu');
        do {
            curl_multi_exec($mh, $running);
        } while ($running);
        LoadTime::end('gmu');

        foreach ($urls as $id => $link) {
            if ($http_code) {
                $result[$id] = curl_getinfo($curl[$id], CURLINFO_HTTP_CODE);
            } else {
                $result[$id] = curl_multi_getcontent($curl[$id]);
            }
            curl_multi_remove_handle($mh, $curl[$id]);
        }
        curl_multi_close($mh);
        return $result;
    }

    public static function get_http_code($url) {
        $ch      = curl_init($url);
        $options = [
            CURLOPT_HEADER         => true,
            CURLOPT_NOBODY         => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
            CURLOPT_TIMEOUT        => TIMEOUT
        ];
        curl_setopt_array($ch, $options);
        curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $http_code;
    }

    public static function match($regex, $content) {
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
        if ($source == '') {
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
            if ($key == $parent) {
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

    public static function get_image_size($img) {
        $img_data = getimagesize($img);
        if ($img_data) {
            preg_match('/width="(.*?)"/', $img_data[3], $tmp);
            $width = $tmp[1];

            preg_match('/height="(.*?)"/', $img_data[3], $tmp);
            $height = $tmp[1];
            return ['width' => $width, 'height' => $height];
        }
    }
}
