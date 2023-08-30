<?php

// $ip   = $_SERVER['REMOTE_ADDR'];
// $file = "ips.json";

// if (!file_exists($file)) {
//     file_put_contents($file, json_encode([]));
// }

// $content = file_get_contents($file);
// $ips = json_decode($content, true);

// if (!isset($ip, $ips)) {
//     $ips[$ip] = ['count' => 1];
// } else {
//     $ips[$ip]['count']++;
// }

// array_multisort(array_column($ips, 'count'), SORT_ASC, $inventory);

// $content = json_encode($ips);
// file_put_contents($file, $content);

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

if (DEBUG && isset($_GET['debug'])) {
    define('_DEBUG', true);
}

require ABSPATH . INC . 'video.php';

Performance::now('total');


$video = new Video($data);
$data  = $video->get_links();


Debug::log('load_time', Performance::now('total'));

Debug::log('memory_usage', (memory_get_peak_usage(true) / 1024 / 1024) . 'MiB');

if (defined('_DEBUG')) {
    Debug::log('data', $data);

    Debug::print();
} else {
    echo json_encode($data);
}
