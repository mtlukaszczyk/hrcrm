<?php

/**
 * Autoloader
 */
function autoload($class_name) {

    $params = explode('\\', $class_name);

    if ($params[0] == 'Engine') {
        require_once 'engine/class_' . strtolower($params[1]) . '.php';
    } else if ($params[0] == 'App') {
        if ($params[1] == 'Controller') {
            if ($params[2] !== 'Controller') {
                require_once 'app/controller/' . $params[2] . 'Controller.php';
            } else {
                require_once 'app/controller/Controller.php';
            }
        } else if ($params[1] == 'Model') {
            require_once 'app/model/' . $params[2] . '.php';
        } else if ($params[1] == 'Helper') {
            require_once 'app/helper/' . $params[2] . '.php';
        } else if ($params[1] == 'Middleware') {
            require_once 'app/middleware/' . $params[2] . '.php';
        } 
    } else {
        echo 'Failed to autoload: ' . $class_name;
        die();
    }
}

spl_autoload_register('autoload');
