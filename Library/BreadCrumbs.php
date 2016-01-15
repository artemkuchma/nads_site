<?php


class BreadCrumbs
{

    private function recursBreadCrumbsArray($menu_array, $val, $new_array)
    {
        foreach ($menu_array as $v) {
            $alias = $v['alias_main_menu'];
            if (Router::getLanguage() != Config::get('default_language')) {
                $alias = Router::getLanguage() . '/' . $v['alias_main_menu'];
            }
            if ($alias == $val) {
                $new_array[] = array(
                    'name' => $v['name'],
                    'alias' => $val
                );
            }
            if (isset($v['child'])) {
                return self::recursBreadCrumbsArray($v['child'], $val, $new_array);
            }
        }
        return $new_array;
    }

    private function getBreadCrumbsAliasName($alias_array = array())
    {
        $menu_array = MenuController::getMainMenuArray();
        $bread_crumbs_array = array();
        foreach ($alias_array as $val) {
            $bread_crumbs_array = self::recursBreadCrumbsArray($menu_array, $val, $bread_crumbs_array);
        }
        return $bread_crumbs_array;
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

        $bread_crumbs_array = self::getBreadCrumbsAliasName($alias_array);

        if (Config::get('bread_crumbs_last_element_view') != 'yes') {
            array_pop($bread_crumbs_array);
        }
        return Controller::render_bread_crumbs($bread_crumbs_array);
    }

}