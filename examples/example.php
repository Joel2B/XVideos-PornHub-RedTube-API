<?php

$data = file_get_contents('https://appsdev.cyou/xv-ph-rt/api/?site_id=xvideos&video_id=59934029');

echo '<pre>';
echo json_encode(json_decode($data), JSON_PRETTY_PRINT);
echo '</pre>';
