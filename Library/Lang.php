<?php

class Lang
{
    private static $url_translation;
    private static $static_translation;

    public static function getStaticTranslation()
    {
        return self::$static_translation;
    }

    public static function getUrlTranslation()
    {
        return self::$url_translation;
    }

    public static function url_translation($lang)
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

    //    if(isset($url_alias[Router::getId()]['alias_' . $lang])){
      //      throw new Exception ( 'Translation the page with ID = '.Router::getId().' is not exist', 204);
       // }
        if(isset($url_alias[Router::getId()]['alias_' . $lang])){
            $url_arr[] = $url_alias[Router::getId()]['alias_' . $lang];
        }

        $url_translation = '/' . implode('/', $url_arr);
        self::$url_translation = $url_translation;
        return $url_translation;

    }

}