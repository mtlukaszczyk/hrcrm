<?php

namespace App\Middleware;

use Engine\Request as Request;

trait Auth {

    public static function checkAutorization() {
        
        if (!self::isLogged() && !self::isLoginAction()) {
            if (\Engine\Request::$isAjax) {
                self::render('json', ['data' => 'err', 'message' => 'not-logged']);
            }

            self::render('logIn.twig');
            die();
        } else {
            
            return true;
        }
    }
    
    public static function isLoginAction() {
        
        return (Request::$controller == 'Account' && Request::$action == 'log_in');
        
    }
}