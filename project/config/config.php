<?php
$serverConf = 'local';

define('CONFIG', [
    'CHARSET' => 'UTF-8',
    'TITLE' => 'HR-MANAGER',
    'META' => [
        'VIEWPORT' => 'width=device-width, initial-scale=1'
    ],
    'USER_PAGE' => [
        403 => true,
        404 => true
    ],
    'SERVER_CONF' => $serverConf,
    'SERVER_MAINTENANCE' => false,
    'HASH' => [
        'TYPE' => PASSWORD_BCRYPT,
        'COST' => 12
    ],
    'REDIS' => false,
]);

define('MAIN_CONTROLLER', 'Account');
define('MAIN_TEMPLATE_DIR', 'index');
define('LANG_SYMBOL', 'app');