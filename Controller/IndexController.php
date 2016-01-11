<?php


class IndexController extends Controller {

    public function indexAction(Request $request)
    {
      //  $indexModel = new IndexModel();
      //  $data = $indexModel->getBook($request->get('id'));
      //  $args = $data[0];

        $args = array(
            'text'=>'Страница индекс индекс'
        );
        return $this->render($args);
    }
    public function testAction (Request $request)
    {
        $this->rewrite_file_alias();
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