<?php

namespace Engine;

class Router {

    private static $controllerFullName;
    private static $actionFullName;

    public static function init() {

        if (trim(Request::$controller) == "" || trim(Request::$action) == "") {
            trigger_error('404: Controller or action missing');
            die();
        } else {
            self::$controllerFullName = ucfirst(fixName(Request::$controller));

            \App\Controller\Controller::inject('user', new \Engine\User(session::get('user')));
            \App\Controller\Controller::init();

            $controllerFullName = '\\App\Controller\\' . (self::$controllerFullName);

            self::$actionFullName = fixName(Request::$action);

            if (!class_exists($controllerFullName)) {
                trigger_error('404: Controller ' . $controllerFullName . ' missing');
                die();
            }
        }

        $reflexion = new \ReflectionClass($controllerFullName);
        $calledInitClass = '\\' . ($reflexion->getMethod('init')->class);

        if ($calledInitClass == $controllerFullName) {
            $controllerFullName::init();
        }
    }

    public static function callAction() {

        $controllerFullName = '\\App\\Controller\\' . self::$controllerFullName;
        $actionFullName = self::$actionFullName;
        
        if ($controllerFullName::beforeRender()) {

            if (method_exists($controllerFullName, $actionFullName)) {

                call_user_func_array([$controllerFullName, $actionFullName], Request::$params);
            } else {
                trigger_error('YPHP: 404: Action ' . $actionFullName . ' in ' . $controllerFullName . ' missing');
                self::renderError(404);
                die();
            }
        } else {
            App::redirect();
        }
    }

    public static function renderError($errNum) {
        global $twig;

        if ($errNum == 404) {
            if (CONFIG['USER_PAGE'][404]) {
                echo $twig->render('special/404.twig');
            }

            header('HTTP/1.0 404 Not found');
            die();
        } else if ($errNum == 403) {
            if (CONFIG['USER_PAGE'][404]) {
                echo $twig->render('special/403.twig');
            }

            header('HTTP/1.0 403 Forbidden');
            die();
        }
    }

}
