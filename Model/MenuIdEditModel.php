<?php


class MenuIdEditModel extends MenuModel
{

    private $new_id_menu_items;
    public $main_menu;

    public function __construct(Request $request, $lang)
    {
        $this->main_menu = $this->getMainMenu($lang);
        $new_id_menu_items = array();
        foreach ($this->main_menu as $v) {
            $key = $v['id_page'] . '-' . $v['id_parent_page'];
            $new_id_menu_items[$key] = $request->post($key);
        }
        $this->new_id_menu_items = $new_id_menu_items;
    }

    public function isValid()
    {
        $full_arr = count($this->new_id_menu_items);
        $unique_arr = count(array_unique($this->new_id_menu_items));
        return $full_arr == $unique_arr ? true : false;
    }

    public function isEmpty()
    {
        foreach ($this->new_id_menu_items as $v) {
            if (empty($v)) {
                return false;
            }
        }
        return true;
    }

    public function isNumberTrue()
    {
        foreach ($this->new_id_menu_items as $v) {
            if ($v > 999 || $v < 1) {
                return false;
            }
        }
        return true;
    }

    public function isNumeric()
    {
        foreach ($this->new_id_menu_items as $v) {
            if (!is_numeric($v)) {
                return false;
            }
        }
        return true;
    }


    public function insertIdMenuItems()
    {
        $dbc = Connect::getConnection();
        foreach ($this->new_id_menu_items as $k => $v) {
            $key_arr = explode('-', $k);

            $placeholders = array(
                'id_page' => $key_arr[0],
                'id_parent_page' => $key_arr[1],
                'id' => $v
            );

            $sql = "UPDATE `main_menu` SET `id`= :id WHERE `id_page`= :id_page AND `id_parent_page`= :id_parent_page";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);
        }
    }

     public function getMaxId()
    {
        $placeholders = array();
        $dbc = Connect::getConnection();
        $sql = "SELECT MAX(id) AS max_id FROM `main_menu` ";
        $data = $dbc->getDate($sql, $placeholders);
        return $data[0]['max_id'];
    }

}