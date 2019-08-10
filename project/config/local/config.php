<?php
/** Session panel */
define('SESSION_PANEL', 'app');

/** App Version */
define('APP_VERSION', 'local');

if (APP_VERSION == 'local') {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}

/** Base URL */
define('base_url', 'http://10.0.0.11/');
define('BASE_SERVER_URL', '/var/www/html/');