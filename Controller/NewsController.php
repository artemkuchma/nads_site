<?php


class NewsController extends Controller
{

    private function cropString($string, $limit, $after = '')
    {
        if (strlen($string) > $limit) {
            $substring_limited = substr($string, 0, $limit); //режем строку от 0 до limit

            return substr($substring_limited, 0, strrpos($substring_limited, ' ')) . $after;
        } else
            return $string;
    }

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
        }


      //  Debugger::PrintR($data);
        $args = array(
            'data' => $data
        );


        return $this->render_news_block($args);


    }

}