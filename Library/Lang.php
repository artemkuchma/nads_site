<?php

class Lang
{
    private static $url_translation;
    private static $static_translation;

    public static function getStaticTranslation($key)
    {
        $lang = Router::getLanguage();
        //print_r(self::$static_translation);
        return isset(self::$static_translation[strtolower($key)][$lang])? self::$static_translation[strtolower($key)][$lang]:self::$static_translation[strtolower($key)][Config::get('default_language')];
    }

    public static function getUrlTranslation()
    {
        return self::$url_translation;
    }

    public static function url_translation($lang, $id_default_page = 1)
    {
        $url_arr = array();
        // скрывает язык установленный по умолчанию
        if ($lang != Config::get('default_language')) {
            $url_arr[] = $lang;
        }
        require LIB_DIR . 'alias.php';
        if (Router::getRout()) {
            $rout_pattern = $url_patterns[Router::getRout()]['pattern_' . $lang];
            $rout_part = str_replace('/.*', '', $rout_pattern);
            $url_arr[] = $rout_part;
        }
        if (isset($url_alias[Router::getId()]['alias_' . $lang])) {
            $url_arr[] = $url_alias[Router::getId()]['alias_' . $lang];
        } else {
            $url_arr[] = Router::get_alis_by_id($id_default_page, $lang);
        }
        $url_translation = '/' . implode('/', $url_arr);
        self::$url_translation = $url_translation;
        return $url_translation;
    }

    public static function load_static_translation()
    {
        $path_static_translation = LANG_DIR. 'translation.php';
        if($path_static_translation){
           // include($path_static_translation);
            self::$static_translation = include($path_static_translation);
        }
        else {
            throw new Exception('Lang file not found: '.$path_static_translation );
        }

    }

}