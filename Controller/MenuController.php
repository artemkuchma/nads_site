<?php


class MenuController extends Controller
{
    private static $main_menu_array;
    private static $admin_menu_array;
    private static $main_menu_part;
    private static $idArray;

    public static function getIdArray()
    {
        return self::$idArray;
    }


    public static function getMainMenuPart()
    {
        return self::$main_menu_part;
    }


    public static function getAdminMenuArray()
    {
        return self::$admin_menu_array;
    }


    public static function getMainMenuArray()
    {
        return self::$main_menu_array;
    }

    public function mainMenuAction()
    {
        $menuModel = new MenuModel();
        $date = $menuModel->getMainMenu(Router::getLanguage());
        $args = self::menuArray($date);
        self::$main_menu_array = $args;

        if (Session::hasUser('admin')) {
            $menuModel = new MenuModel();
            $date = $menuModel->getAdminMenu();
            $args_admin = self::menuArray($date);
            foreach ($args_admin as $k => $v) {
                if ($v['alias_menu'] == 'admin') {
                    unset($v['child']);
                    $args[$k] = $v;
                }
            }

        }

        return $this->render_main_menu($args);
    }

    public function menuArray($date)
    {

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


        return $args; //$this->render_main_menu($args);//render($args);

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

    public static function menu_recurs($array = array(), $id_class = null, $active_page_class = null)
    {
        $menu_class = isset($id_class) ? $id_class : '';
        echo '<ul' . $menu_class . '>';
        $lang = '';
        $class = '';

        if (Router::getLanguage() != Config::get('default_language')) {
            $lang = Router::getLanguage() . '/';
        }
        foreach ($array as $v) {

            if (isset($v['child'])) {
                if(isset($active_page_class) && $v['id_page'] == Router::getId()){
                    $class = $active_page_class;
                }
                echo '<li > <a href="/' . $lang . $v['alias_menu'] . '" class="'.$class.'" >' . $v['name'] . '</a>';
                $class = '';

                self::menu_recurs($v['child'], $id_class = null, $active_page_class);
                echo '</li>';
            } else {

                if(isset($active_page_class) && $v['id_page'] == Router::getId()){
                    $class = $active_page_class;
                }
                echo '<li > <a href="/' . $lang . $v['alias_menu'] . '"class="'.$class.'"  >' . $v['name'] . '</a></li>';
                $class = '';
            }

        }
        echo '</ul>';
    }

    public static function getById($array, $id_page)
    {
        foreach ($array as $val) {
            if ($val['id_page'] == $id_page) {
                self::$main_menu_part = $val;
            } elseif (isset($val['child'])) {
                self::getById($val['child'], $id_page);
            }

        }

    }

    public static function idArray($array)
    {
        foreach ($array as $k => $val) {
            self::$idArray[$k] = $k;
            if (isset($val['child'])) {
                self::idArray($val['child']);
            }
        }
    }


    /**
    public static function menu_recurs($array = array(), $dropdown_menu='')
    {
    $menu_class = isset($dropdown_menu)? $dropdown_menu : 'nav navbar-nav';
    echo '<ul class="'.$menu_class.'">';
    $lang = '';
    if (Router::getLanguage() != Config::get('default_language')) {
    $lang = Router::getLanguage() . '/';
    }
    foreach ($array as $v) {

    if (isset($v['child'])) {
    echo '<li class="dropdown"> <a href="/' . $lang . $v['alias_menu'] . '" class="dropdown-toggle" data-toggle="dropdown">' . $v['name'] . '<span class="caret"></span></a>';
    $dropdown_menu = 'dropdown-menu';
    self::menu_recurs($v['child'], $dropdown_menu);
    echo '</li>';
    }else{
    echo '<li> <a href="/' . $lang . $v['alias_menu'] . '">' . $v['name'] . '</a></li>';
    }

    }
    echo '</ul>';
    }
     * */

    public function adminMenuAction()
    {
        $menuModel = new MenuModel();
        $date = $menuModel->getAdminMenu();
        $args = self::menuArray($date);
        $args[0] = array(
            'id_page' => '',
            'id_parent_page' => 0,
            'status' => 1,
            'name' => 'Вернуться на сайт',
            'alias_menu' => '',
            'level' => 1
        );
        self::$admin_menu_array = $args;
        return $this->render_admin_menu($args);

    }

    public function getBlockMainMenuAction()
    {
        $block_menu_array = array();
        $page_with_menu_block = array();
        $block_menu = array();

        foreach (self::getMainMenuArray() as $k => $v) {
            self::getById(self::getMainMenuArray(), $k);
            if (isset($v['child'])) {
                $block_menu_array[$k] = self::getMainMenuPart();
                $array_for_id = self::getMainMenuPart()['child'];
                self::$idArray = array();
                self::idArray($array_for_id);
                $page_with_menu_block[$k] = self::getIdArray();
                $page_with_menu_block[$k][$k] = $k;
            }
        }
        foreach($page_with_menu_block as $k => $v){
            $id_page = Router::getId();
            if(isset($v[$id_page])){
                $block_menu[] = $block_menu_array[$k];
            }
        }

        $args = array(
            'block_menu' => $block_menu,
            'pages' => $page_with_menu_block
        );

        return $this->render_menu_block($args);
    }


}