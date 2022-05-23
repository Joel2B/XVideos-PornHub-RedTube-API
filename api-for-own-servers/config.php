<?php

define('HOST', 'http://localhost'); // change to your own host
define('PATH', HOST . dirname($_SERVER['PHP_SELF']) . '/');
define('BYPASS_URL', 'https://tmp02.appsdev.cyou/bypass-js-check');
define('COOKIE_EXT', '.cookie');
define('SITES_INFO', PATH . 'data/sites-info.json');
define('CONNECTTIMEOUT', 3);
define('TIMEOUT', 4);
