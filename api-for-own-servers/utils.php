<?php

function get_url_content($url) {
    $curl = curl_init();

    $options = array(
        CURLOPT_URL            => $url,
        CURLOPT_AUTOREFERER    => true,
        CURLOPT_FRESH_CONNECT  => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_CONNECTTIMEOUT => CONNECTTIMEOUT,
        CURLOPT_TIMEOUT        => TIMEOUT,
        CURLOPT_RETURNTRANSFER => true,
    );

    curl_setopt_array($curl, $options);

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}
