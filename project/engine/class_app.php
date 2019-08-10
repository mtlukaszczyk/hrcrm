<?php
namespace Engine;

class App {
    /**
     * Redirects to given controller action
     * @param string $controller
     * @param string $action
     */
    public static function redirect($controller = '', $action = '', $params = []) {

        $link = self::getLink(LANG_SYMBOL, $controller, $action, $params);
        header("Location: " . $link);
        die();
    }

    public static function getLink($lang_symbol = LANG_SYMBOL, $controller = '', $action = '', $params = []) {
        if ($action == '') {
            if ($controller == '') {
                $link = base_url . $lang_symbol . '/';
            } else {
                $link = base_url . $lang_symbol . '/' . $controller . '/index/';
            }
        } else {
            $link = base_url . $lang_symbol . "/" . $controller . '/' . $action . '/' . implode('/', $params);
        }

        return $link;
    }
}