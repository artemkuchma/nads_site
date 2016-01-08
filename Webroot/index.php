<?php
require_once '../Library/init.php';

//RouterClass::parse($_SERVER['REQUEST_URI']);
try{
 $content = Router::get_content_by_uri($_SERVER['REQUEST_URI']);
}catch (Exception $e){
    $content = Router::get_content_by_uri('error_404');
    IndexController::errorAction($e);

    // $content = Router::get_content_by_uri('error_404', $e);
   // if($e->getCode()== 403){
     //   $content = Controller::redirect('error_403');
   // }
    //else{
      //  $content = Controller::redirect('error_404');
   // }
}
echo $content;

echo '<pre>';
print_r('Action: '.Router::getAction().PHP_EOL);
print_r('Controller: '.Router::getController().PHP_EOL);
print_r('Lang: '.Router::getLanguage().PHP_EOL);
print_r('URL: ' .Router::getUri().PHP_EOL);
print_r('ID: ' .Router::getId() .PHP_EOL);
echo 'Params: ';
print_r(Router::getParams());
echo '</pre>';


//$url=$_SERVER['REQUEST_URI'];
//print_r($url);
