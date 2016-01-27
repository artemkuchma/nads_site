<?php


class deleteModel {

    private $id;

    public function __construct($id)
    {
        $this->id = $id;

    }

    private function change_id_parents($set_value_parents_id)
    {
        $placeholders = array(
            'id' => $this->id,
            'set_value_parents_id'=> $set_value_parents_id
        );
        $dbc = Connect::getConnection();
        $sql = "UPDATE `main_menu` SET `id_parent_page`=:set_value_parents_id WHERE id_parent_page = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

    }

    public function delete()
    {
        $this->change_id_parents(0);

        $placeholders = array(
            'id' => $this->id,
        );
        $dbc = Connect::getConnection();
        $sql = "DELETE FROM `pages` WHERE id = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

    }

}