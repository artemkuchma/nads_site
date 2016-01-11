<?php
require_once '../Config/conf.php';

function __autoload($className)
{
    $file = "{$className}.php";
    if(file_exists(CONF_DIR . $file)){
        require_once CONF_DIR . $file;
    } elseif (file_exists(CONTROLLER_DIR . $file)){
        require_once CONTROLLER_DIR . $file;
    } elseif (file_exists(LANG_DIR . $file)){
        require_once LANG_DIR . $file;
    } elseif (file_exists(LIB_DIR . $file)){
        require_once LIB_DIR .$file;
    } elseif (file_exists(MODEL_DIR . $file)){
        require_once MODEL_DIR .$file;
    } elseif (file_exists(VIEW_DIR . $file)){
        require_once VIEW_DIR . $file;
    } elseif (file_exists(WEBROOT_DIR . $file)){
        require_once WEBROOT_DIR . $file;
    } else {
        throw new Exception ("{$file} not found", 404);
    }
}

function __t($key)
{
 return Lang::getStaticTranslation($key);
}