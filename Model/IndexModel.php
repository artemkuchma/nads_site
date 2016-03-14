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
        $sql = "SELECT p.id, p.status, bp.img {$fields_list} FROM pages p JOIN {$material_type} bp JOIN {$material_type}_{$lang} bp_{$lang}
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
        } else {
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

    public function getViews($material_type)
    {
        $dbc = Connect::getConnection();
        $lang = Router::getLanguage();
        $sql = "SELECT * FROM `{$material_type}_{$lang}`";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);

        return $date;
    }

    public function getNewsBlok($items_per_block)
    {
        $dbc = Connect::getConnection();
        $lang = Router::getLanguage();
        $placeholders = array(// 'items_per_block' => $items_per_block
        );

        $sql = "SELECT n_{$lang}.title, n_{$lang}.description, n_{$lang}.text, n_{$lang}.alias, n.date, n.img FROM news_{$lang} n_{$lang} JOIN news n ON n.id = n_{$lang}.id_news ORDER BY n.date DESC LIMIT {$items_per_block}";

        $data = $dbc->getDate($sql, $placeholders);

        return $data;
    }

    public function getBasicPageBlock()
    {
        $dbc = Connect::getConnection();

        $placeholders = array();

        $sql = "SELECT `id_page` FROM `pages_in_block`";

        $d = $dbc->getDate($sql, $placeholders);
        $data = array();
        if (!empty($d)) {
            $id_pages = '';
            foreach ($d as $v) {
                $id_pages .= ',' . $v['id_page'];
            }
            $id_pages = substr($id_pages, 1);


            $placeholders = array(//  'id_pages' => $id_pages
            );


            $lang = Router::getLanguage();
            $sql = "SELECT bp_{$lang}.title, bp_{$lang}.alias, bp.id_page FROM basic_page_{$lang} bp_{$lang} JOIN basic_page bp ON bp_{$lang}.id_basic_page = bp.id AND bp.id_page IN({$id_pages})";

            $data = $dbc->getDate($sql, $placeholders);
        }
        return $data;

    }
}