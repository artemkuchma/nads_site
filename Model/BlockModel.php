<?php


class BlockModel
{
    private $id_array = array();
    private $delete_array = array();

    public function __construct(Request $request)
    {
        for ($i = 1; $i <= Config::get('basic_page_in_block'); $i++) {
            $id = $request->post("id_$i");
            if ($id) {
                $this->id_array[$i] = $id;
            }
            $delete = $request->post("delete_$i");
            if (isset($delete)) {
                $this->delete_array[$i] = $delete;
            }
        }
    }

    public function isNumeric()
    {
        foreach ($this->id_array as $v) {
            if (!is_numeric($v)) {
                if ($v != null) {
                    return false;
                }
            }
        }
        return true;
    }

    public function isExist()
    {
        $dbc = Connect::getConnection();
        $placeholders = array();
        $sql = "SELECT `id_page` FROM `basic_page`";
        $data = $dbc->getDate($sql, $placeholders);
        $d = array();
        foreach ($data as $key => $val) {
            $d[$val['id_page']] = $val['id_page'];
        }
        if (!empty($this->id_array)) {

            foreach ($this->id_array as $v) {
                if (!isset($d[$v])) {
                    return $v;
                }
            }
        }
        return null;
    }

    public function update()
    {
        $dbc = Connect::getConnection();
        $placeholders = array();
        $sql = "DELETE FROM `pages_in_block`";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);


        $this->id_array = array_diff_key($this->id_array, $this->delete_array);
       // Debugger::PrintR($this->id_array);
        if (!empty($this->id_array)) {
            $values = '';
            foreach ($this->id_array as $v) {
                if ($v) {
                    $values .= ',(' . $v . ',' . $v . ')';
                }
            }
            $values = substr($values, 1);


            $sql = "INSERT INTO `pages_in_block`(`id`, `id_page`) VALUES {$values}";
          //  echo $sql;
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);
        }
    }

    public function getDeleteArray()
    {
        return $this->delete_array;
    }

    public function getIdArray()
    {
        return $this->id_array;
    }


}