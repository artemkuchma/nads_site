<?php
require_once '../Library/init.php';

//RouterClass::parse($_SERVER['REQUEST_URI']);
try{
 $content = Router::get_content_by_uri($_SERVER['REQUEST_URI']);
}catch (Exception $e){

    $content = Router::get_content_by_uri('index/error', $e);
}
echo $content;

echo '<pre>';
print_r('Action: '.Router::getAction().PHP_EOL);
print_r('Controller: '.Router::getController().PHP_EOL);
print_r('Lang: '.Router::getLanguage().PHP_EOL);
print_r('Prefix: '.Router::getMethodPrefix().PHP_EOL);
print_r('Rout: '.Router::getRout().PHP_EOL);
echo 'Params: ';
print_r(Router::getParams());
echo '</pre>';


//$url=$_SERVER['REQUEST_URI'];
//print_r($url);
