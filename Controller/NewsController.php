<?php


class NewsController extends Controller {

    public function indexAction()
    {
        $args = $this->index('news');

        return $this->render($args);
    }

}