<?php

class Router {
    private static $uri;
    private static $controller;
    private static $action;
    private static $params = array();
    private static $rout;
    private static $method_prefix;
    private static $language;

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

    public static function getRout()
    {
        return self::$rout;
    }

    public static function getUri()
    {
        return self::$uri;
    }

    public static function getLanguage()
    {
        return self::$language;
    }

    public static function getMethodPrefix()
    {
        return self::$method_prefix;
    }

    public static function parse($uri)
    {
        self::$uri = urldecode(trim($uri, '/'));
        self::$action = Config::get('default_action');
        self::$controller = Config::get('default_controller');
        self::$language = Config::get('default_language');
        self::$rout = Config::get('default_rout');
        $routes = Config::get('routs');
        self::$method_prefix = isset($routes[self::$rout])? $routes[self::$rout]:'';

        $uri_parts = explode('?', self::$uri);

        $first_part = $uri_parts[0];

        $uri_array = explode('/', $first_part);
        $uri_elements = array();
        // Удаляет все специальные символы и кирилицу из элементов массива (заменяет на "_"), чтоб срабатывало исключение
        foreach($uri_array as $val){
            $uri_element = preg_replace ("/[^a-zA-Z0-9]/","_",$val);
            $uri_elements[] = $uri_element;
        }

        if(count($uri_elements)){
            if(in_array(strtolower(current($uri_elements)),array_keys($routes))){
                self::$rout = strtolower(current($uri_elements));
                self::$method_prefix = isset($routes[self::$rout])? $routes[self::$rout] : '';
                array_shift($uri_elements);
            }
            elseif(in_array(strtolower(current($uri_elements)),Config::get('languages'))){
                self::$language = strtolower(current($uri_elements));
                array_shift($uri_elements);
            }
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
        }
    }

    public static function get_content_by_uri($uri, Exception $e=null)
    {
        self::parse($uri);
        $request = new Request();
        $_controller = self::getController().'Controller';
        $_action = self::getAction().'Action';

        $_controller_object = new $_controller;

        if(!method_exists($_controller_object, $_action)){
            throw new Exception("{$_action} not found", 404);
        }
        $content = $_controller_object -> $_action($request, $e);
        return $content;
    }

    public static function alias_to_url($alias)
    {
        
        $url = '';
        return $url;
    }


}