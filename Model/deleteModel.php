<?php


class deleteModel
{

    private $id;

    public function __construct($id)
    {
        $this->id = $id;

    }

    private function recurs_delete($dbc, $data,$placeholders)
    {
        if (count($data)) {
            foreach ($data as $v) {
                $id_t = $v['id_page'];

                $sql = "SELECT id_page FROM `main_menu` WHERE id_parent_page = {$id_t}";
                $data = $dbc->getDate($sql, $placeholders);

                $sql = "DELETE FROM `main_menu` WHERE id_page = {$id_t}";
                $sth = $dbc->getPDO()->prepare($sql);
                $sth->execute($placeholders);

                $this->recurs_delete($dbc,$data,$placeholders);
            }
        }

    }

    private function delete_from_menu()
    {
        $placeholders = array(
            'id' => $this->id,
        );
        $dbc = Connect::getConnection();
        $sql = "SELECT id_page FROM `main_menu` WHERE id_parent_page = :id";
        $data = $dbc->getDate($sql, $placeholders);
        // Debugger::PrintR($date);

        $sql = "DELETE FROM `main_menu` WHERE id_page = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

        $this->recurs_delete($dbc, $data,$placeholders);
    }

    public function delete()
    {
        $this->delete_from_menu();

        $placeholders = array(
            'id' => $this->id,
        );
        $dbc = Connect::getConnection();
        $sql = "DELETE FROM `pages` WHERE id = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);
    }

}