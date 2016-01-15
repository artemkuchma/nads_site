<?php


class MenuController extends Controller
{
    private static $main_menu_array;

    public static function getMainMenuArray()
    {
        return self::$main_menu_array;
    }

    public function mainMenuAction()
    {
        $menuModel = new MenuModel();
        $date = $menuModel->getMainMenu(Router::getLanguage());

        //заменил ключи массива date на id страниц (id страниц из массивов не удалял)
        $d = array();
        foreach ($date as $v) {
            $d[$v['id_page']] = $v;
        }
        // получил уровень(уровень который занимает каждый пункт в меню)
        // для каждого пункта, также вычислил максимальный имеющийся уровень
        $levels = self::getMenuLevelAction($d);
        $sort_levels = $levels;
        rsort($sort_levels);
        $max_level = $sort_levels[0];
        // добавил уровни в массивы для каждого пункта
        foreach ($d as $k => $v) {
            $d[$k]['level'] = $levels[$k];
        }
        // получение многомерного массива для главного меню
        $lev = array();
        for ($i = $max_level; $i >= 1; $i--) {
            $l = 'L_' . $i;
            $l = array();
            foreach ($d as $k => $v) {
                if ($v['level'] == $i) {
                    $l[$k] = $v;
                }
            }
            $lev[] = $l;
        }
        for ($i = 0; $i < $max_level - 1; $i++) {
            foreach ($lev[$i] as $k => $v) {
                if (isset($lev[$i + 1][$v['id_parent_page']])) {
                    $lev[$i + 1][$v['id_parent_page']]['child'][$k] = $v;
                }
            }
        }
        $args = $lev[$max_level - 1];
         self::$main_menu_array = $args;

/**
        $l_1 = array();
        foreach ($d as $k => $v) {
            if ($v['level'] == 1) {
                $l_1[$k] = $v;
            }
        }
        $l_2 = array();
        foreach ($d as $k => $v) {
            if ($v['level'] == 2) {
                $l_2[$k] = $v;
            }
        }
        $l_3 = array();
        foreach ($d as $k => $v) {
            if ($v['level'] == 3) {
                $l_3[$k] = $v;
            }
        }

        foreach ($l_3 as $k => $v) {
            if (isset($l_2[$v['id_parent_page']])) {
                $l_2[$v['id_parent_page']]['child'][$k] = $v;
            }
        }
        foreach ($l_2 as $k => $v) {
            if (isset($l_1[$v['id_parent_page']])) {
                $l_1[$v['id_parent_page']]['child'][$k] = $v;
            }
        }

**/

        echo'<pre>';
       // print_r(self::getMenuLevelAction($d));
        echo'</pre>';
        echo'<pre>';
      // print_r($args);
        echo'</pre>';

        return $this->render_main_menu($args);//render($args);

    }


    public static function getMenuLevelAction($d = array())
    {
        $level = array();
        foreach ($d as $k => $v) {
            $i = 1;
            $key = 'id_parent_page';

            $level[$k] = self::recurs($d, $v, $key, $i);

        }
        return $level;
    }

    private function recurs($d, $v, $key, $i)
    {
        if ($v[$key] != 0) {
            $i = $i + 1;
            return self::recurs($d, $d[$v[$key]], $key, $i);
        } else {

            return $i;
        }
    }

    public static function menu_recurs($array = array(),$main_teg_open,$main_teg_close,$teg_open, $teg_close){
        echo $main_teg_open;
        $lang ='';
        if(Router::getLanguage()!=Config::get('default_language')){
            $lang = Router::getLanguage().'/';
        }
        foreach($array as $v){
            echo $teg_open.'<a href="/'.$lang.$v['alias_main_menu'].'">'.$v['name'].'</a>'.$teg_close;
            if(isset($v['child']) ){
                self::menu_recurs($v['child'],$main_teg_open,$main_teg_close,$teg_open, $teg_close);

            }
        }
        echo $main_teg_close;
    }


}