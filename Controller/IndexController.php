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

    public function errorAction(Request $request, Exception $e)
    {
        $args = array(
            'message'=> 'test-m  ',//$e->getMessage(),
        );
        return $this->render($args);
    }


}