<?php

include 'encryption.php';

if (!isset($_GET['data']) || empty($_GET['data'])) {
    die();
}

$url = (new Encryption('', false))->decrypt($_GET['data']);
if ($url != '') {
    header("Location: $url");
    die();
}
