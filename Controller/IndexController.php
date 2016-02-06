<?php


class IndexController extends Controller {

    private function index()
    {

    }

    public function indexAction()
    {
        //$this->rewrite_file_alias();
        $indexModel = new IndexModel();
        $data = $indexModel->getPage(Router::getId(), Router::getLanguage(),'basic_page');
        if (!$data) {
            throw new Exception(" Page is not exist", 404);

        }
        elseif($data[0]['status'] == 0){

            throw new Exception(" Page not publish", 2);
        }

        if(!$indexModel->existTranslationPage(Router::getId(), Router::getLanguage(),'basic_page')){
            throw new Exception(" Page has no translation", 204);
        }
        $args = $data[0];


        return $this->render($args);
    }


    public static  function errorAction(Exception $e)
    {
        $date = date('Y-m-d H:i:s') .PHP_EOL;
        $date .= '/./ '.$e->getCode().PHP_EOL;
        $date .= '/./ '.$e->getMessage().PHP_EOL;
        $date .= '/./ '.$e->getFile().PHP_EOL;
        $date .= '/./ '.$e->getLine().PHP_EOL;
        $date .= '///';


       self::rewrite_file(WEBROOT_DIR.'log.txt','a', $date);
    }

    public function contactAction(Request $request)
    {
        $form = new ContactModel($request);
        $msg = $request->get('msg');

        if ($request->isPost()) {
            if ($form->isValid()) {
                $this->redirect('/_index.php?rout=index/contact&id=3&msg=Сообщение отправленно');
                $form->saveToDb();
                mail('ts@test.com', 'HELLOW', $form->name . PHP_EOL . $form->email . PHP_EOL . $form->message. PHP_EOL. $form->date);
                $form->name = '';
                $form->email = '';
                $form->message = '';
            }else{
                $msg = 'Fail!!!';
            }
        }
        $args = array(
            'form' => $form,
            'msg' => $msg
        );


        return $this->render($args);
    }
/**
    public function deleteAction()
    {
        if (Session::hasUser('admin')){


            $adminController = new AdminController();
            $adminController->deleteAction();

        }else {
            throw new Exception('Access  denied', 403);
        }

    }

**/


}