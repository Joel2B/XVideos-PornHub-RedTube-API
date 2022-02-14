<?php

// TODO: security, legibility and more

$allowed_ips = [
    '*',
];

$user_ip = $_SERVER['REMOTE_ADDR'];

if (!in_array($user_ip, $allowed_ips) && !in_array('*', $allowed_ips)) {
    die();
}

if (empty($_GET['url'])) {
    die();
}

include 'HTTP/Request2.php';
include 'config.php';
include 'server.php';

$url    = $_GET['url'];
$cookie = [
    'action' => $_GET['cookie_action'] ?? 'read',
    'id'     => $_GET['cookie_id'] ?? '',
];

$server = new Server($url, $cookie);

header('Content-type: text/plain');

echo $server->get_content();
