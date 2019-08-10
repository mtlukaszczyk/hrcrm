<?php

namespace App\Controller;

use Engine\Session as Session;

class Controller {
    
    use \App\Middleware\Auth;

    protected static $vars;
    public static $user;

    /*
     * Dependency injection
     */

    public static function inject($name, $data) {
        self::${$name} = $data;
    }

    /**
     * Class initializator
     */
    public static function init() {

        self::$vars = [];
        \Engine\Assets::globals();
    }

    /**
     * Set variable for using it in template
     * @param string $name variable name
     * @param mixed $var variable value
     */
    public static function set($name, $var) {
        self::$vars[$name] = $var;
    }

    /**
     * Checks is user logged
     * @return boolean
     */
    public static function isLogged() {

        if (Session::check('user')) {
            $user_data = Session::get('user');
            if (is_numeric($user_data['id'])) {
                if (Session::get('ip') == $_SERVER['REMOTE_ADDR'] && Session::get('user-agent') == $_SERVER['HTTP_USER_AGENT']) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Renders template
     * @param string $templateName
     * @param array $data
     * @param string $type
     */
    public static function render($templateName, $data = [], $type = "twig") {
        global $twig, $action;

        self::$vars = $data;
        self::$vars['base_url'] = base_url;
        self::$vars['lang_symbol'] = LANG_SYMBOL;

        if ($templateName !== "json") {

            self::renderHeader();

            if ($type == "std") {
                //$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "index";
                extract($GLOBALS);
                extract(self::$vars);

                include('app/template/std/' . $templateName);
            } else if ($type == "twig") {
                self::$vars['class'] = get_called_class();

                if (self::isLogged()) {

                    $userSession = \Engine\session::get('user');

                    $user = [
                        'email' => self::$user->getEmail(),
                        'id' => self::$user->getID(),
                    ];

                    self::$vars['user'] = $user;
                    self::$vars['action'] = $action;
                }

                $new = array_merge($GLOBALS, self::$vars);

                echo $twig->render($templateName, $new);
            }

            self::renderFooter();
        } else if ($templateName == "json") {
            unset(self::$vars['base_url']);
            unset(self::$vars['lang_symbol']);
            echo json_encode(self::$vars);
        }

        die();
    }

    private static function renderHeader() {
        echo "<!DOCTYPE html>" . PHP_EOL . "<html>" . PHP_EOL . "    <head>" . PHP_EOL;

        if (isset(CONFIG['TITLE'])) {
            echo '        <title>' . CONFIG['TITLE'] . '</title>' . PHP_EOL;
        }

        if (isset(CONFIG['CHARSET'])) {
            echo '        <meta http-equiv="Content-Type" content="text/html; charset=' . CONFIG['CHARSET'] . '" />' . PHP_EOL;
        }

        if (isset(CONFIG['META'])) {

            if (is_array(CONFIG['META'])) {
                foreach (CONFIG['META'] as $key => $meta) {
                    echo '        <meta name="' . $key . '" content="' . $meta . '"/>' . PHP_EOL;
                }
            }
        }

        \Engine\Assets::render();


        echo "        <script type='text/javascript'>" . PHP_EOL;

        echo "			  var base_url = '" . base_url . "';" . PHP_EOL;
        echo "                    var isLogged = " . (self::isLogged() ? 'true' : 'false') . ";" . PHP_EOL;

        echo "		  </script>" . PHP_EOL;

        echo "    </head>" . PHP_EOL;
        echo "    <body>" . PHP_EOL;
    }

    private static function renderFooter() {
        \Engine\Assets::render('down');
        echo "    </body>" . PHP_EOL . "</html>";
    }

    /**
     * Checked every time before rendering. Here you can define individual rules
     * @param string $controller
     * @param string $action
     * @return boolean true if has access
     */
    public static function beforeRender() {
        
        return self::checkAutorization();
    }

}
