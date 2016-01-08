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
        $args = array(
            'text'=>'Еще одна тестовая страница'
        );
        return $this->render($args);
    }

    public static  function errorAction(Exception $e)
    {
        //добавить запись всех этих данных в лог файл
        $args = array(
            'message'=> $e->getMessage(),
            'cod' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        );

       // return $this->render($args);
    }


}