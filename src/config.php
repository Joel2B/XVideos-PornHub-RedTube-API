<?php

define('HOST', 'https://appsdev.cyou'); // change to your own host, for example: http://localhost
define('CORS', false);
define('PATH', HOST . dirname($_SERVER['PHP_SELF']) . '/');
define('BYPASS_URL', 'https://tmp02.appsdev.cyou/bypass-js-check');
define('CONNECTTIMEOUT', 3);
define('TIMEOUT', 4);
define('DEBUG_PAGE', true);
