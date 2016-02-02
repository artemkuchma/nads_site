<?php


class DeleteModel
{

    private $id;
    private $edit;
   // private $material_type;

    public function __construct($id, $edit =null)
    {
        $this->id = $id;
        $this->edit = isset($edit)? $edit : null;
       // $this->material_type = $material_type;

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
        $placeholders = array();
        $sql = "SELECT `type_name` FROM type_of_materyals tm JOIN pages p ON p.id = {$id} AND p.id_mat_type = tm.id";
        $d = $dbc->getDate($sql, $placeholders);
        $material_type = $d[0]['type_name'];

        $lang = Router::getLanguage();


        $sql = "SELECT bp_{$lang}.alias FROM {$material_type}_{$lang} bp_{$lang} JOIN {$material_type} bp ON bp.id_page = {$id} AND bp.id = bp_{$lang}.id_{$material_type}";
        $data = $dbc->getDate($sql, $placeholders);
        if($data[0]['alias']){
            $alias_arr = explode('/', $data[0]['alias']);
            $simple_alias = array_pop($alias_arr);

            $sql ="UPDATE {$material_type}_{$lang} bp_{$lang} JOIN {$material_type} bp SET bp_{$lang}.alias = '".$simple_alias."' WHERE bp.id_page = {$id} AND bp.id = bp_{$lang}.id_{$material_type}";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);

        }

    }

}