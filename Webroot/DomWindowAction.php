<?php
require_once '../Webroot/DomWindow.php';

$adminModel = new AdminModel();
$request = new Request();
if($request->isPost()){
//Debugger::PrintR($_POST);

    $adminModel->updateStaticTranslation($request);

}else{
    Session::setFlash('Тестовое сообщение');
}
Controller::rewrite_file_translation();
Controller::redirect('/admin/translations_static_text');