<?php

class Router
{
    private static $uri;
    private static $controller;
    private static $action;
    private static $params = array();
    private static $language;
    private static $id;
    private static $rout;


    private static function url_to_array($uri)
    {
        self::$uri = urldecode(trim($uri, '/'));
        $uri_parts = explode('?', self::$uri);

        $first_part = $uri_parts[0];

        $uri_array = explode('/', $first_part);
        $uri_elements = array();
        // Удаляет все специальные символы и кирилицу из элементов массива (заменяет на "_"), чтоб срабатывало исключение
        foreach ($uri_array as $val) {
            $uri_element = preg_replace("/[^a-zA-Z0-9]/", "_", $val);
            $uri_elements[] = $uri_element;
        }
        return $uri_elements;
    }

    /**
     * private static function lang_by_url($uri)
    {
    $lang = self::$language = Config::get('default_language');

    $uri_elements = self::url_to_array($uri);

    if(count($uri_elements)){
    if(strtolower(current($uri_elements)!= 'admin')){
    if(in_array(strtolower(current($uri_elements)),Config::get('languages'))){
    $lang = self::$language = strtolower(current($uri_elements));

    }
    }
    }
    return $lang;
    }
     **/

    private static function find_alias($url)
    {
        require LIB_DIR . 'alias.php';
        require LIB_DIR . 'patterns.php';

        foreach ($url_patterns as $k => $v) {
            $regex = $v['pattern_' . self::getLanguage()];
            //echo $regex;
            if (preg_match('@^' . $regex . '$@', $url, $match)) {
                // echo "OK";
                $url_parts = explode('/', $match[0]);
                //print_r($url_parts);
                //echo $val['elements_before_alias'];
                for ($i = 1; $i <= $v['elements_before_alias']; $i++) {
                    array_shift($url_parts);
                }
                // print_r($url_parts);
                $url = implode('/', $url_parts);

                if (isset($v['action'])) {
                    $action = $v['action'];
                    self::$action = $action;
                }

                if (isset($v['controller'])) {
                    $controller = $v['controller'];
                    self::$controller = $controller;
                }
                $rout = $k;
                if ($rout) {
                    self::$rout = $rout;
                }
                //echo $urll;
            }
        }

        if ($url) {
            // $url_alias_ = 'url_alias_'.self::getLanguage();
            // print_r($$url_alias_) ;
            $result = '';
            foreach ($url_alias as $key => $val) {
                if (isset($val['alias_' . self::getLanguage()])) {
                    if ($val['alias_' . self::getLanguage()] == $url) {
                        self::$controller = isset($controller) ? $controller : $val['controller'];
                        self::$action = isset($action) ? $action : $val['action'];
                        self::$id = $key;
                        $result = 'found';
                    }
                }
            }
            if ($result != 'found') {

                throw new Exception('Page (' . $_SERVER['REQUEST_URI'] . ') not found', 404);
            }
        }

        //echo $url;
    }

    public static function getRout()
    {
        return self::$rout;
    }


    public static function getAction()
    {
        return self::$action;
    }

    public static function getController()
    {
        return self::$controller;
    }

    public static function getParams()
    {
        return self::$params;
    }

    public static function getUri()
    {
        return self::$uri;
    }

    public static function getLanguage()
    {
        return self::$language;
    }

    public static function getId()
    {
        return self::$id;
    }

    public static function parse($uri)
    {
        self::$action = Config::get('default_action');
        self::$controller = Config::get('default_controller');
        self::$language = Config::get('default_language');
        self::$id = Config::get('default_id');

        $uri_elements = self::url_to_array($uri);

        if (count($uri_elements)) {
            if (strtolower(current($uri_elements) != 'admin')) {
                if (in_array(strtolower(current($uri_elements)), Config::get('languages'))) {
                    self::$language = strtolower(current($uri_elements));
                    array_shift($uri_elements);
                }
            } else {
                array_shift($uri_elements);
            }
            $url = implode('/', $uri_elements);
            self::find_alias($url);


            /**
            if(current($uri_elements)){
            self::$controller = ucfirst(strtolower(current($uri_elements)));
            array_shift($uri_elements);
            }
            if(current($uri_elements)){
            self::$action = strtolower(current($uri_elements));
            array_shift($uri_elements);
            }
            if(current($uri_elements)){
            self::$params = $uri_elements;

            }
             **/
        }
    }

    public static function get_content_by_uri($uri)
    {
        self::parse($uri);
        Lang::load_static_translation();
        $request = new Request();
        $_controller = self::getController() . 'Controller';
        $_action = self::getAction() . 'Action';

        $_controller_object = new $_controller;

        if (!method_exists($_controller_object, $_action)) {
            throw new Exception("{$_action} not found", 404);
        }
        $content = $_controller_object->$_action($request);
        return $content;
    }

    public static function get_alis_by_id($id, $languages)
    {
        require LIB_DIR . 'alias.php';

        return $url_alias[$id]['alias_' . $languages];
    }


}