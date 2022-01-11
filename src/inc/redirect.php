<?php

include 'encryption.php';

if (empty($_GET['data'])) {
    die();
}

$url = (new Encryption('', false))->decrypt($_GET['data']);

if (!empty($url)) {
    header("Location: $url");
    die();
}
