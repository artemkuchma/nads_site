<?php


class IndexModel {
    public function getBook($id)
    {
        $dbc = Connect::getConnection();
        $sql = 'SELECT * FROM `pages_ukr` WHERE id = :id';
        $placeholders = array('id'=> $id);
        $date = $dbc->getDate($sql, $placeholders);
        if(!$date){
            throw new Exception("id = $id ,is not exist", 404);
        }
        return $date;

    }

}