<?php

namespace Engine;

class Redis {

    private static $connection;

    /**
     * Initialize class
     */
    public static function init() {
        self::$connection = new \Predis\Client();
    }

    public static function getInstance() {
        return self::$connection;
    }

    public static function __callStatic($name, $args) {

        return call_user_func_array([self::$connection, $name], $args);
    }

}
