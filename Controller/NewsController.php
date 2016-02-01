<?php


class NewsController extends Controller {

    public function indexAction()
    {
        $indexModel = new IndexModel();
        $data = $indexModel->getPage(Router::getId(), Router::getLanguage(),'news');
        if (!$data) {
            throw new Exception(" Page is not exist", 404);

        }
        elseif($data[0]['status'] == 0){

            throw new Exception(" Page not publish", 2);
        }
        $args = $data[0];


        return $this->render($args);
    }



}