<?php
require_once '../Library/init.php';

//RouterClass::parse($_SERVER['REQUEST_URI']);

try{
    $content = Router::get_content_by_uri($_SERVER['REQUEST_URI']);
}catch (Exception $e){
     IndexController::errorAction($e);
    //$content = $e->getMessage();
    $lang = Router::getLanguage();
    //$content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(5, $lang));
   // $content = Router::get_content_by_uri('en/error_404');
    if($e->getCode()== 403){
        $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(7, $lang));
    }
    elseif($e->getCode()== 204){
        $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(7, $lang));
    }
    else{
    $content = Router::get_content_by_uri($lang . '/'. Router::get_alis_by_id(5, $lang));
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
