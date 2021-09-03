<?php

function get_url_content($url, $cookie = '') {
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
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT        => 4,
        CURLOPT_RETURNTRANSFER => true
        // CURLOPT_SSL_VERIFYPEER => false
    ];
    if ($cookie == 'create') {
        $options[CURLOPT_COOKIEJAR] = dirname(__FILE__) . '\tmp\cookie.txt';
    } else {
        $options[CURLOPT_COOKIEFILE] = dirname(__FILE__) . '\tmp\cookie.txt';
    }
    curl_setopt_array($curl, $options);
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

header('Content-type: text/plain');

if (!isset($_GET['url']) || empty($_GET['url'])) {
    die();
}

$url    = urldecode($_GET['url']);
$cookie = isset($_GET['cookie']) ? $_GET['cookie'] : '';
echo get_url_content($url, $cookie);
