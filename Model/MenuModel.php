<?php


class MenuModel {

    public function getMainMenu($lang)
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT mm.id_page, mm.id_parent_page, mm.status, mm_{$lang}.name, mm_{$lang}.alias_menu FROM main_menu mm JOIN main_menu_{$lang} mm_{$lang} ON mm.id =
mm_{$lang}.id_main_menu";
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

}