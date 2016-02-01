<?php


class IndexController extends Controller {

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
        $args = $data[0];


        return $this->render($args);
    }
    public function testAction (Request $request)
    {

        $args = array(
            'text'=>'Еще одна тестовая страница'
        );
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