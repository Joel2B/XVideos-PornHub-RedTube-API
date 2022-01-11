<?php

include '../../config.php';
include 'vtt.php';
include '../encryption.php';

// indicate that the content will be cached
$seconds_to_cache = 60 * 60 * 24 * 30;
$ts               = gmdate('D, d M Y H:i:s', time() + $seconds_to_cache) . ' GMT';

header('Content-type: text/vtt');
header("Expires: $ts");
header('Pragma: cache');
header("Cache-Control: max-age=$seconds_to_cache");
Header('Access-Control-Allow-Origin: *');

if (empty($_GET['data'])) {
    die();
}

$data = (new Encryption('', false))->decrypt($_GET['data']);

if (!empty($data)) {
    $data = json_decode($data, true);
    $vtt  = new Vtt($data);

    $vtt->print_vtt();
}
