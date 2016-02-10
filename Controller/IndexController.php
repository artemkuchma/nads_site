<?php


class IndexController extends Controller
{


    public function indexAction()
    {
        //$this->rewrite_file_alias();
        $args = $this->index('basic_page');

        return $this->render($args);
    }


    public static function errorAction(Exception $e)
    {
        $date = date('Y-m-d H:i:s') . PHP_EOL;
        $date .= '/./ ' . $e->getCode() . PHP_EOL;
        $date .= '/./ ' . $e->getMessage() . PHP_EOL;
        $date .= '/./ ' . $e->getFile() . PHP_EOL;
        $date .= '/./ ' . $e->getLine() . PHP_EOL;
        $date .= '///';


        self::rewrite_file(WEBROOT_DIR . 'log.txt', 'a', $date);
    }

    public function contactAction(Request $request)
    {
        $data_page = $this->index('basic_page');
        $form = new ContactModel($request);

        if ($request->isPost()) {
            if ($form->isValid()) {
                $form->saveToDB();
                mail(Config::get('admin_email'), "Сообщение от пользователя $form->name ", $form->name . PHP_EOL . $form->email . PHP_EOL . $form->message . PHP_EOL . $form->date);
                $form->name = '';
                $form->email = '';
                $form->message = '';
                Session::setFlash(__t('message_send'));
            } else {
                Session::setFlash(__t('message_not_send'));
            }
        }
        $args = array(
            'form' => $form,
            'data_page' => $data_page,
        );

        return $this->render($args);
    }

    public function viewsNewsAction()
    {
        $data_page = $this->index('basic_page');
        $indexModel = new IndexModel();
        $data_news_arr = $indexModel->getViews('news');

        $items_count = count($data_news_arr);
        $items_per_page = Config::get('news_per_page');

        $request = new Request();
        $currentPage = $request->get('page') ? (int)$request->get('page') : 1;
        $data_pagination = self::getPagination($items_count, $items_per_page, $currentPage);

        if ($items_count) {
            $data_news_page = array_chunk($data_news_arr, $items_per_page, true);
            if (isset($data_news_page[$currentPage - 1])) {
                $data_news_page = $data_news_page[$currentPage - 1];
            } else {
                throw new Exception('Page (' . Router::getUri() . ') not found', 404);
            }
        } else {
            $data_news_page = null;
        }
        $data_url = explode('?', Router::getUri());

        $lang = Router::getLanguage()==Config::get('default_language')? '' : Router::getLanguage().'/';

        $args = array(
            'data_page' => $data_page,
            'data_news' => $data_news_page,
            'data_pagination' => $data_pagination,
            'data_url' => $data_url[0],
            'lang' => $lang
        );

        return $this->render($args);
    }


}