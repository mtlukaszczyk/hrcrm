<?php
session_start();

require_once 'config/config.php';
require_once 'config/' . CONFIG['SERVER_CONF'] . '/config.php';
require_once 'config/' . CONFIG['SERVER_CONF'] . '/database.php';

require_once 'vendor/autoload.php';

if (APP_VERSION == 'local') {
    $whoops = new \Whoops\Run;
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);

    if (\Whoops\Util\Misc::isAjaxRequest()) {
        $jsonHandler = new \Whoops\Handler\JsonResponseHandler();
        $jsonHandler->setJsonApi(true);
        $whoops->pushHandler($jsonHandler);
    }

    $whoops->register();
}

// ILLUMINATE ELOQUENT CAPSULE

use Illuminate\Database\Capsule\Manager as DB;

$capsule = new DB;

$capsule->addConnection([
    'driver' => DB_CFG['DRIVER'],
    'host' => DB_CFG['HOST'],
    'database' => DB_CFG['NAME'],
    'username' => DB_CFG['USER'],
    'password' => DB_CFG['PASSWORD'],
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->getConnection()->enableQueryLog();

// Set the event dispatcher used by Eloquent models... (optional)

use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

$dispatcher = new Dispatcher(new Container);

$capsule->setEventDispatcher($dispatcher);
$capsule->setAsGlobal();

$capsule->bootEloquent();

$loader = new Twig_Loader_Filesystem('app/template');

$twig = new Twig_Environment($loader, [
    'cache' => false,
        ]);

$twig->addExtension(new Twig_Extension_Debug());

require_once 'app/function/TwigFunctions.php';

foreach($twigFunctions as $function) {
    $twig->addFunction($function);
}

require_once 'engine/base_functions.php';
require_once 'engine/autoload.php';

Engine\Assets::init();

if (CONFIG['REDIS']) {
    Engine\Redis::init();
}
Engine\Request::decode();
Engine\Router::init();
Engine\Router::callAction();