<?php


class BreadCrumbs
{


    private function breadCrumbsArray($alias_array = array())
    {
        $menuModel = new MenuModel();
        $data = $menuModel->getMainMenu(Router::getLanguage());

        $data_menu_key_alias = array();
        foreach ($data as $k => $v) {
            $data_menu_key_alias[$v['alias_menu']] = $v;
        }
        $bread_crumbs_arr = array();
        foreach ($alias_array as $val) {
            if (Router::getLanguage() != Config::get('default_language')) {
                $k = str_replace(Router::getLanguage() . '/', '', $val);
            } else {
                $k = $val;
            }
            if (isset($data_menu_key_alias[$k])) {
                $bread_crumbs_arr[] = array(
                    'name' => $data_menu_key_alias[$k]['name'],
                    'alias' => $val
                );
            }
        }
        return $bread_crumbs_arr;
    }


    public static function getBreadcrumbs()
    {

        $url_elements = Router::getUrlArray();
        if (count($url_elements)) {
            if (strtolower(current($url_elements)) == 'admin') {
                array_shift($url_elements);
            }
        }
        $alias_array = array();
        $i = 0;
        if (Router::getLanguage() != Config::get('default_language')) {
            $i = 1;
        }

        while (count($url_elements) > $i) {
            $ue = $url_elements;
            $alias_array[] = implode('/', $ue);
            array_pop($url_elements);
        }
        $alias_array = array_reverse($alias_array);

        $bread_crumbs_array = self::breadCrumbsArray($alias_array);

        if (Config::get('bread_crumbs_last_element_view') != 'yes') {
            array_pop($bread_crumbs_array);
        }
        return Controller::render_bread_crumbs($bread_crumbs_array);
    }

}