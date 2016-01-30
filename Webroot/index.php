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
    else{
    $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(Config::get('default_id_error_404'), $lang));
    }
}
echo $content;
/**
echo '<pre>';
print_r('Action: '.Router::getAction().PHP_EOL);
print_r('Controller: '.Router::getController().PHP_EOL);
print_r('Lang: '.Router::getLanguage().PHP_EOL);
print_r('URL: ' .Router::getUri().PHP_EOL);
print_r('ID: ' .Router::getId() .PHP_EOL);
echo 'Params: ';
print_r(Router::getParams());
echo '</pre>';
**/

//$url=$_SERVER['REQUEST_URI'];
//print_r($url);
