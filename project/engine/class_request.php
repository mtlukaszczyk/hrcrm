<?php
namespace Engine;

class Request {

    public static $type;
    public static $controller;
    public static $action;
    public static $params = [];
    public static $requestParams = [];
    public static $isAjax = false;
    public static $customRoute = false;

    public static function decode() {

        self::$type = $_SERVER['REQUEST_METHOD'];
        $params = [];
        $reqParams = [];

        self::$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

        $controller = "";
        $action = "";
        $get = [];

        if (isset($_REQUEST['custom'])) {
            if ($_REQUEST['custom'] == 1) {
                self::$customRoute = true;
                $controller = $_REQUEST['controller'] ?? MAIN_CONTROLLER;
                $action = $_REQUEST['action'] ?? "index";
            }

            $get_params = explode('/', ($_REQUEST['params'] ?? ""));
            $get = array_merge([$_REQUEST['controller'], $_REQUEST['action']], $get_params);
        }

        if (isset($_REQUEST['params']) && self::$customRoute === false) {

            $get = explode('/', ($_REQUEST['params'] ?? ""));

            $controller = $get[0] ?? MAIN_CONTROLLER;
            $action = $get[1] ?? "index";
        }

        if (self::$isAjax) {

            $controller = fixName(ucfirst($controller));

            $f = new \ReflectionMethod('\App\Controller\\' . $controller, fixName($action));
            $params = [];

            if (self::$type == 'DELETE' || self::$type == 'PUT') {
                parse_str(file_get_contents("php://input"), $reqParams);
            } else {
                $reqParams = $_REQUEST;
            }

            foreach ($f->getParameters() as $param) {
                $params[$param->name] = $reqParams[$param->name] ?? null;
            }
        } else {

            $controller = $controller !== "" ? $controller : MAIN_CONTROLLER;
            $action = $action !== "" ? $action : "index";

            $params = [];

            for ($i = 2, $cGet = count($get); $i < $cGet; $i++) {
                $params[] = $get[$i];
            }
        }

        self::$controller = $controller;
        self::$action = $action;
        self::$params = $params;
        self::$requestParams = $reqParams;
    }

}
