<?php


class DeleteModel
{

    private $id;
    private  $edit;

    public function __construct($id, $edit =null)
    {
        $this->id = $id;
        $this->edit = isset($edit)? $edit : null;

    }

    private function recurs_delete($dbc, $data,$placeholders)
    {
        if (count($data)) {
            foreach ($data as $v) {
                $id_t = $v['id_page'];

                $sql = "SELECT id_page FROM `main_menu` WHERE id_parent_page = {$id_t}";
                $data = $dbc->getDate($sql, $placeholders);

                if($this->edit){
                    echo 'test ';
                    $this->edit_alias($dbc, $id_t);
                }

                $sql = "DELETE FROM `main_menu` WHERE id_page = {$id_t}";
                $sth = $dbc->getPDO()->prepare($sql);
                $sth->execute($placeholders);



                $this->recurs_delete($dbc,$data,$placeholders);
            }
        }

    }

    public  function delete_from_menu()
    {
        $placeholders = array(
            'id' => $this->id,
        );
        $dbc = Connect::getConnection();
        $sql = "SELECT id_page FROM `main_menu` WHERE id_parent_page = :id";
        $data = $dbc->getDate($sql, $placeholders);

       if($this->edit){
            $this->edit_alias($dbc, $this->id);
        }

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

    private function edit_alias($dbc,  $id)
    {
        $lang = Router::getLanguage();
        $placeholders = array();

        $sql = "SELECT bp_{$lang}.alias FROM basic_page_{$lang} bp_{$lang} JOIN basic_page bp ON bp.id_page = {$id} AND bp.id = bp_{$lang}.id_basic_page";
        $data = $dbc->getDate($sql, $placeholders);
        if($data[0]['alias']){
            $alias_arr = explode('/', $data[0]['alias']);
            $simple_alias = array_pop($alias_arr);

            $sql ="UPDATE basic_page_{$lang} bp_{$lang} JOIN basic_page bp SET bp_{$lang}.alias = '".$simple_alias."' WHERE bp.id_page = {$id} AND bp.id = bp_{$lang}.id_basic_page";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);

        }

    }

}