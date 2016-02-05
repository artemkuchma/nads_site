<?php


class IndexModel
{


    public function getPage($id, $lang, $material_type)
    {
        $fields = new FieldsModel($material_type);

        $fields_list = '';
        foreach ($fields->getFields() as $v) {
            if ($v != 'id' && $v != 'alias' && $v != 'id_' . $material_type . '') {
                $fields_list .= ', bp_' . $lang . '.' . $v . ' ';
            }
        }

        $dbc = Connect::getConnection();
        $sql = "SELECT p.id, p.status {$fields_list} FROM pages p JOIN {$material_type} bp JOIN {$material_type}_{$lang} bp_{$lang}
        WHERE p.id = :id AND p.id = bp.id_page AND  bp.id = bp_{$lang}.id_{$material_type}";
        $placeholders = array('id' => $id);
        $date = $dbc->getDate($sql, $placeholders);

        return $date;

    }


    public function getType_of_Materials()
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT id, type_name FROM  type_of_materyals";
        $placeholders = array();
        $data = $dbc->getDate($sql, $placeholders);
        return $data;

    }

    public function getTotalList($material_type = 'all')
    {

        $data = $this->getType_of_Materials();

        if ($material_type == 'all') {
            foreach ($data as $v) {
                if ($v['type_name'] != 'Admin') {
                    $sql_arr[] = "SELECT tom.type_name AS type_materials, p.id, p.status, {$v['type_name']}_en.alias AS alias_en, {$v['type_name']}_en.title AS title_en, {$v['type_name']}_uk.alias AS alias_uk, {$v['type_name']}_uk.title AS title_uk FROM type_of_materyals tom JOIN
pages p JOIN `{$v['type_name']}` JOIN `{$v['type_name']}_en` JOIN `{$v['type_name']}_uk` ON (tom.id = p.id_mat_type AND p.id = {$v['type_name']}.id_page AND {$v['type_name']}.id =
{$v['type_name']}_en.id_{$v['type_name']}) AND (tom.id = p.id_mat_type AND p.id = {$v['type_name']}.id_page AND {$v['type_name']}.id = {$v['type_name']}_uk.id_{$v['type_name']} )";
                }
            }
        }else{
            $sql_arr[] = "SELECT tom.type_name AS type_materials, p.id, p.status, {$material_type}_en.alias AS alias_en, {$material_type}_en.title AS title_en, {$material_type}_uk.alias AS alias_uk, {$material_type}_uk.title AS title_uk FROM type_of_materyals tom JOIN
pages p JOIN `{$material_type}` JOIN `{$material_type}_en` JOIN `{$material_type}_uk` ON (tom.id = p.id_mat_type AND p.id = {$material_type}.id_page AND {$material_type}.id =
{$material_type}_en.id_{$material_type}) AND (tom.id = p.id_mat_type AND p.id = {$material_type}.id_page AND {$material_type}.id = {$material_type}_uk.id_{$material_type} )";

        }
        $dbc = Connect::getConnection();
        $sql = implode(' UNION ALL ', $sql_arr);
        $placeholders = array();
        $data = $dbc->getDate($sql, $placeholders);
        return $data;
    }

    public function getCount($material_type = 'all')
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT type_name FROM  type_of_materyals";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
        if ($material_type == 'all') {
            foreach ($date as $v) {
                if ($v['type_name'] != 'Admin') {
                    $sql_arr[] = "SELECT count(*) AS itemsCount FROM {$v['type_name']}";
                }
            }
            $sql = implode(' UNION ALL ', $sql_arr);
        } else {
            $sql = "SELECT count(*) AS itemsCount FROM $material_type";
        }
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
        $date_sum = 0;
        foreach ($date as $v) {
            $date_sum += $v['itemsCount'];
        }
        return $date_sum;
    }

    public function existTranslationPage($id, $lang, $material_type)
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT bp_{$lang}.title FROM {$material_type}_{$lang} bp_{$lang} JOIN {$material_type} bp ON bp_{$lang}.id_{$material_type} = bp.id AND bp.id_page = {$id}";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);

        return empty($date[0]['title']) ? false : true;
    }


}