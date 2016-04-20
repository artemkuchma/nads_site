<?php

class FieldsModel {

    private $fields;

    public function __construct($material_type)
    {
        $this->fields = $this->takeFields($material_type);

    }

    private function takeFields($material_type)
    {
        $lang = Config::get('default_language');
        $dbc = Connect::getConnection();
        $sql = "SELECT COLUMN_NAME
FROM INFORMATION_SCHEMA.COLUMNS
WHERE table_name = '".$material_type."_".$lang."'";

        $placeholders = array();
        $d = $dbc->getDate($sql, $placeholders);

        $data =array();

        foreach($d as $v){
            $data[]=$v['COLUMN_NAME'];
        }

        $data = array_unique($data);
        return $data;
    }
    public function getFields()
    {
        return $this->fields;
    }
}