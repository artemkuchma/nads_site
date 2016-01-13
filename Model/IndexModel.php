<?php


class IndexModel {
    public function getPage($id, $lang)
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT p.id, p.status, bp_{$lang}.title, bp_{$lang}.text FROM pages p JOIN basic_page bp JOIN basic_page_{$lang} bp_{$lang}
        WHERE p.id = :id AND p.id = bp.id_page AND  bp.id = bp_{$lang}.id_basic_page";
        $placeholders = array('id'=> $id);
        $date = $dbc->getDate($sql, $placeholders);
        if(!$date || $date[0]['status'] == 0){
            throw new Exception("id = $id ,is not exist", 404);
        }
        return $date;

    }

}