<?php
namespace Engine;

class Session {

    public static function set($name, $val) {
        if (!isset($_SESSION[SESSION_PANEL])) {
            $_SESSION[SESSION_PANEL] = [];
        }

        $_SESSION[SESSION_PANEL][$name] = $val;
    }

    public static function get($name) {
        if (isset($_SESSION[SESSION_PANEL][$name])) {
            return $_SESSION[SESSION_PANEL][$name];
        } else {
            return null;
        }
    }

    public static function check($name) {
        return isset($_SESSION[SESSION_PANEL][$name]);
    }

    public static function remove($name) {
        unset($_SESSION[SESSION_PANEL][$name]);
    }

    public static function getFull() {
        if (isset($_SESSION[SESSION_PANEL])) {
            return $_SESSION[SESSION_PANEL];
        } else {
            return null;
        }
    }
}