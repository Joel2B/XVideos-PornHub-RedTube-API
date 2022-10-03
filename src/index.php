<?php

include 'config.php';

if (CORS && isset($_SERVER['HTTP_ORIGIN'])) {
    $origin = $_SERVER['HTTP_ORIGIN'];

    $allowed_domains = [
        'https://example.com',
        'http://localhost',
    ];

    if (in_array($origin, $allowed_domains, true)) {
        header("Access-Control-Allow-Origin: $origin");
    }
} else {
    header("Access-Control-Allow-Origin: *");
}

header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

header('Content-Type: application/json');

$data = [];

if (!empty($_GET['data'])) {
    $data['data'] = $_GET['data'];
} else if (!empty($_GET['site_id']) && !empty($_GET['video_id'])) {
    $data['site_id']  = $_GET['site_id'];
    $data['video_id'] = $_GET['video_id'];
} else {
    die();
}

include 'inc/video.php';

$video = new Video($data);
$data  = $video->get_links();

echo json_encode($data);
