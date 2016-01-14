<?php


class IndexController extends Controller {

    public function indexAction()
    {
      // BreadCrumbs::getBreadcrumbs();

        //$this->rewrite_file_alias();
        $indexModel = new IndexModel();
        $data = $indexModel->getPage(Router::getId(), Router::getLanguage());
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
        $date .= $e->getCode().PHP_EOL;
        $date .= $e->getMessage().PHP_EOL;
        $date .= $e->getFile().PHP_EOL;
        $date .= $e->getLine().PHP_EOL;
        $date .= ''.PHP_EOL;

       self::rewrite_file(WEBROOT_DIR.'log.txt','a', $date);
    }


}