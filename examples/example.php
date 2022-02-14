<?php

$data = file_get_contents('https://appsdev.cyou/xv-ph-rt/api/?site_id=xvideos&video_id=59934029');

echo '<pre>';
print_r(json_decode($data, true));
echo '</pre>';
