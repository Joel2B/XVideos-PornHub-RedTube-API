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

if (
    !isset($_GET['site_id']) || empty($_GET['site_id']) ||
    !isset($_GET['video_id']) || empty($_GET['video_id'])
) {
    die('{}');
}

$site_id  = $_GET['site_id'];
$video_id = $_GET['video_id'];
$video    = new Video($site_id, $video_id);
$data     = $video->get_links();
echo json_encode($data);
