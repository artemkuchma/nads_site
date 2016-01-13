<?php


class MenuModel {

    public function getMainMenu($lang)
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT mm.id_page, mm.id_parent_page, mm.status, mm_{$lang}.name, mm_{$lang}.alias_main_menu FROM main_menu mm JOIN main_menu_{$lang} mm_{$lang} ON mm.id =
mm_{$lang}.id_main_menu";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
       // echo'<pre>';
       // print_r($date);
        //echo'</pre>';



        //if(!$date){
          //  throw new Exception("id = $id ,is not exist", 404);
        //}
        return $date;

    }

}