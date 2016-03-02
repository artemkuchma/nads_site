<?php
require_once '../Library/init.php';
Session::start();

try {


    $request = new Request();
    $adminController = new AdminController();
    $key = $request->get('key');
    if(isset($key)){

        $adminController->editStaticTranslation($key);
    }


    if (!isset($key)) {

        $adminController->imgBrowseAction();
    }



} catch (Exception $e) {
    IndexController::errorAction($e);
    $lang = 'en';
    if ($e->getCode() == 403) {
        Controller::redirect('/' . Router::get_alis_by_id(Config::get('default_id_error_403'), $lang));
    } elseif ($e->getCode() == 2) {
        Controller::redirect('/' . Router::get_alis_by_id(Config::get('not_publish'), $lang));
    } elseif ($e->getCode() == 204) {
        Controller::redirect('/' . Router::get_alis_by_id(Config::get('default_id_error_204'), $lang));
    } else {
        Controller::redirect('/' . Router::get_alis_by_id(Config::get('default_id_error_404'), $lang));
    }
}