<?php

require_once 'HTTP/Request2.php';

function get_url_content($url, $create_cookie) {
    $request = new HTTP_Request2();

    $request->setUrl($url);
    $request->setMethod(HTTP_Request2::METHOD_GET);
    $request->setConfig(array(
        'follow_redirects' => true,
    ));

    if (!$create_cookie) {
        $cookies = json_decode(file_get_contents('tmp/cookies.txt'), true);

        foreach ($cookies as $cookie) {
            $request->addCookie($cookie['name'], $cookie['value']);
        }
    }

    $response = $request->send();

    if ($response->getStatus() == 200) {
        if ($create_cookie) {
            file_put_contents('tmp/cookies.txt', json_encode($response->getCookies()));
        }

        return $response->getBody();
    }

    return '';
}

if (empty($_GET['url'])) {
    die();
}

$url           = $_GET['url'];
$create_cookie = isset($_GET['cookie']) && $_GET['cookie'] === 'create';

header('Content-type: text/plain');

echo get_url_content($url, $create_cookie);
