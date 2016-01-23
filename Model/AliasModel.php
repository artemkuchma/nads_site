<?php

class AliasModel
{

    public function getAliasDate()
    {
        $dbc = Connect::getConnection();
        $sql = "SELECT type_name FROM  type_of_materyals";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);

        foreach ($date as $v) {
            if ($v['type_name'] != 'Admin') {
                $sql_arr[] = "SELECT  p.id, p.controller, p.action, {$v['type_name']}_en.alias AS alias_en, {$v['type_name']}_uk.alias AS alias_uk FROM
pages p JOIN `{$v['type_name']}` JOIN `{$v['type_name']}_en` JOIN `{$v['type_name']}_uk` ON (p.id = {$v['type_name']}.id_page AND {$v['type_name']}.id =
{$v['type_name']}_en.id_{$v['type_name']}) AND (p.id = {$v['type_name']}.id_page AND {$v['type_name']}.id = {$v['type_name']}_uk.id_{$v['type_name']} )";
            }else{
                $sql_arr[] = "SELECT p.id, p.controller, p.action, {$v['type_name']}.alias AS alias_en, {$v['type_name']}.alias AS alias_uk FROM
pages p JOIN `{$v['type_name']}` ON p.id = {$v['type_name']}.id_page";
            }
        }
        $sql = implode(' UNION ALL ', $sql_arr);
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);

        $d = array();
        foreach ($date as $v) {
            $d[$v['id']] = $v;
            unset($d[$v['id']]['id']);
        }
        return $d;
    }

}