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
        if ($lang != Config::get('default_language')) {
            $url_arr[] = $lang;
        }
        require LIB_DIR . 'alias.php';
        if (Router::getRout()) {
            $rout_pattern = $url_patterns[Router::getRout()]['pattern_' . $lang];
            $rout_part = str_replace('/.*', '', $rout_pattern);
            $url_arr[] = $rout_part;
        }

        $url_alias = 'url_alias_' . $lang;
        foreach ($$url_alias as $k => $val) {
            if ($val['id'] == Router::getId()) {
                $url_arr[] = $k;
            }
        }
        $url_translation = '/' . implode('/', $url_arr);
        self::$url_translation = $url_translation;
        return $url_translation;

    }

}