<?php


class NewsController extends Controller
{



    public function indexAction()
    {
        $args = $this->index('news');

        return $this->render($args);
    }

    public function getBlockAction()
    {
        $indexModel = new IndexModel();
        $data = $indexModel->getNewsBlok(Config::get('news_in_block'));


        foreach($data as $k => $v){
            if ($v['description']) {

                $data[$k]['short_text'] = $this->cropString($v['description'], 100, '...');
            } elseif ($v['text']) {
                $data[$k]['short_text'] = $this->cropString($v['text'], 100, '...');
            } else {
                $data[$k]['short_text'] = '';
            }
            $data[$k]['date'] = date('y.m.d', strtotime($v['date']));
        }


      //  Debugger::PrintR($data);
        $args = array(
            'data' => $data
        );


        return $this->render_news_block($args);


    }

}