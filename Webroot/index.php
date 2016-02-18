<?php
require_once '../Library/init.php';
Session::start();

try{
    $request = new Request();
    $content = Router::get_content_by_uri($request->server('REQUEST_URI'));
}catch (PDOException $e){
    IndexController::errorAction($e);
//$content = $e->getMessage();
    $lang = Router::getLanguage();
    $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(Config::get('default_id_error_500'), $lang));

}catch (Exception $e){
     IndexController::errorAction($e);
    $lang = Router::getLanguage();
    if($e->getCode()== 403){
        $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(Config::get('default_id_error_403'), $lang));
    }
    elseif($e->getCode() == 2){
        $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(Config::get('not_publish'), $lang));
    }
    elseif($e->getCode() == 204){
        $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(Config::get('default_id_error_204'), $lang));
    }
    else{
    $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(Config::get('default_id_error_404'), $lang));
    }
}
echo $content;

