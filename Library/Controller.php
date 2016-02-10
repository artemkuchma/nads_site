<?php

abstract class Controller
{

    private function file_path($tpl = null)
    {
        $tplDir = Router::getController();
        $tplName = isset($tpl)? $tpl : Router::getAction();
        $templateFile = VIEW_DIR . $tplDir . DS . $tplName . '.phtml';
        if (!file_exists($templateFile)) {
            throw new Exception("{$templateFile} not found", 404);
        }
        return $templateFile;
    }

    public function index($material_type){
        $indexModel = new IndexModel();
        $data = $indexModel->getPage(Router::getId(), Router::getLanguage(),$material_type);
        if (!$data) {
            throw new Exception(" Page is not exist", 404);

        }
        elseif($data[0]['status'] == 0){

            throw new Exception(" Page not publish", 2);
        }

        if(!$indexModel->existTranslationPage(Router::getId(), Router::getLanguage(),$material_type)){
            throw new Exception(" Page has no translation", 204);
        }
        $args = $data[0];
        return $args;
    }

    public static  function render_simple($path,$params1 = null,$params2 = null,$params3 = null,$params4 = null)
    {
        ob_start();
        require $path;
        return ob_get_clean();
    }


    protected function render(array $args = array(), $tpl = null)
    {
        extract($args);

        ob_start();
        require $this->file_path($tpl); //$templateFile;
        $content = ob_get_clean();

        $search = require VIEW_DIR.'searchForm.phtml';

        $menu = new MenuController();
        $main_menu = $menu->mainMenuAction();

        $login_logout = new SecurityController();
        $login_logout_block = $login_logout->logAction();


        if (Router::getLanguage() == 'uk') {
            $lang = 'en';
        } else {
            $lang = 'uk';
        }
        $lang_icon = Lang::url_translation($lang, Config::get('default_id_error_204'));

        $bread_crumbs = BreadCrumbs::getBreadcrumbs();


        ob_start();
        require VIEW_DIR . 'layout.phtml';
        return ob_get_clean();



    }

    protected function render_main_menu(array $args = array())
    {
        ob_start();
        require VIEW_DIR . 'Menu/mainMenu.phtml';

        return ob_get_clean();
    }
    protected function render_admin_menu(array $args = array())
    {
        ob_start();
        require VIEW_DIR . 'Menu/adminMenu.phtml';

        return ob_get_clean();
    }

    protected function render_admin(array $args = array(), $tpl = null)
    {
        extract($args);

        ob_start();
        require $this->file_path($tpl); //$templateFile;
        $content = ob_get_clean();

        $menu = new MenuController();
        $admin_menu = $menu->adminMenuAction();


        ob_start();
        require VIEW_DIR . 'adminLayout.phtml';
        return ob_get_clean();
    }

    public static function render_lang_icon($url_translation)
    {
        ob_start();
        require VIEW_DIR . 'langIcon.phtml';

        return ob_get_clean();
    }

    public static function render_bread_crumbs(array $args = array())
    {
        if (count($args)) {
            ob_start();
            require VIEW_DIR . 'breadCrumbs.phtml';
            $bc = ob_get_clean();
        } else {
            $bc = '';
        }
        return $bc;
    }

    public static function render_login_logout (array $args = array())
    {
        extract($args);
        ob_start();
        require VIEW_DIR . 'Security/log.phtml';
        return ob_get_clean();
    }


    public static function redirect($url)
    {
        header("Location: $url");
        die;
        // exit;
    }



    public static function rewrite_file($file_path, $mode, $date)
    {
        $f = fopen($file_path, $mode);
        fwrite($f, $date);
        fclose($f);
    }

    public static function rewrite_file_alias()
    {
        $aliasModel = new AliasModel();
      //  Debugger::PrintR($aliasModel->getAliasDate());
        $date = '<?php' . PHP_EOL . '$url_alias = array(' . PHP_EOL;
        foreach ($aliasModel->getAliasDate() as $k => $v) {
            $date .= '' . PHP_EOL . '      ' . $k . ' => array(' . PHP_EOL;
            foreach ($v as $key => $val) {
                $date .= "            '" . $key . "' => '" . $val . "'," . PHP_EOL;
            }
            $date .= '      ),' . PHP_EOL . '';
        }
        $date .= ');';

        self::rewrite_file(LIB_DIR . 'alias.php', 'w', $date);
    }

    public function getPagination($itemsCount, $itemsPerPage, $currentPage)
    {
        if($currentPage<0){
            throw new Exception('Bad request' , 400);
        }
        $pagination = new Pagination($currentPage, $itemsCount, $itemsPerPage);
        $pagination_arr = $pagination->buttons;
        return $pagination_arr;
    }

    public static function recurs_render_menu_in_form($array, $data_menu_item = null)
    {

        foreach($array as $k => $v){
            $t ='';
            for($i=1; $i<=$v['level']; $i++){
            $t .='+';
        }  $selected = '';
            if($k == $data_menu_item[0]['id_parent_page']){
                $selected = 'selected';
            }
           echo '<option '.$selected.' value = "'.$v['alias_menu'].'!'.$k.'" >'.$t.' '.$v['name'].'</option>';
            if(isset($v['child'])){
                self::recurs_render_menu_in_form($v['child'],$data_menu_item);
            }
        }
    }

    public static function recurs_render_menu_edit($array)
    {
        echo '<ul>';

        foreach($array as $v){
            echo '<li ><input type="number" required min = 1 max = 999  name = "'.$v['id_page'].'-'.$v['id_parent_page'].'" value = "'.$v['id'].'" >'.$v['name'].'</li>';
            if(isset($v['child'])){
                self::recurs_render_menu_edit($v['child']);
            }
        }
        echo '</ul>';
    }


}