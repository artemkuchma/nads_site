<?php


class IndexController extends Controller {

    public function indexAction(Request $request)
    {
        $indexModel = new IndexModel();
        $data = $indexModel->getBook($request->get('id'));
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

    public function errorAction(Request $request, Exception $e)
    {
        $args = array(
            'message'=> $e->getMessage(),
            'code'=> $e->getCode(),
            'file'=> $e->getFile(),
            'line'=> $e->getLine(),
            'trace'=> $e->getTraceAsString()
        );
        return $this->render($args);
    }


}