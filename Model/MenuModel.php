<?php


class MenuModel {

    public function getMainMenu($lang)
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT mm.id_page, mm.id_parent_page,mm.id, mm.status, mm_{$lang}.name, mm_{$lang}.alias_menu FROM main_menu mm JOIN main_menu_{$lang} mm_{$lang} ON mm.id =
mm_{$lang}.id_main_menu ORDER BY mm.id";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
        return $date;

    }
    public function getAdminMenu()
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT am.id_page, am.id_parent_page, am.status, am.name, am.alias_menu FROM admin_menu am ";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
        return $date;

    }
    public function getMenuDatePage($id_page)
    {
        $lang = Router::getLanguage();
        $dbc = Connect::getConnection();
        $placeholders = array(
            'id_page'=>$id_page
        );
        $sql = "SELECT mm.id as id_menu_item, mm.id_parent_page, mm_{$lang}.name AS name_menu_item, mm_{$lang}.alias_menu, mm.status
         FROM main_menu mm JOIN main_menu_{$lang} mm_{$lang} ON mm.id_page = :id_page AND mm.id = mm_{$lang}.id_main_menu";
        $date = $dbc->getDate($sql, $placeholders);
        return $date;


    }


}