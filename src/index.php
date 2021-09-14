<?php

if (isset($_SERVER['HTTP_ORIGIN']) === true) {
    $origin          = $_SERVER['HTTP_ORIGIN'];
    $allowed_domains = [
        'https://example.com',
        'http://localhost'
    ];

    if (in_array($origin, $allowed_domains, true) === true) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Methods: GET');
        header('Access-Control-Allow-Headers: Content-Type');
    }
}
header('Content-Type: application/json');

include 'config.php';
include 'inc/video.php';

$data = [];

if (!empty($_GET['data'])) {
    $data['data'] = $_GET['data'];
} else if (!empty($_GET['site_id']) && !empty($_GET['video_id'])) {
    $data['site_id']  = $_GET['site_id'];
    $data['video_id'] = $_GET['video_id'];
} else {
    die();
}

$video = new Video($data);
$data  = $video->get_links();
echo json_encode($data);
