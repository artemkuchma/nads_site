<?php


class SearchModel
{

    private $search_request;

    public function __construct(Request $request)
    {
        $search_request = $request->post('search');
        $search_request = trim($search_request);
        $search_request = htmlspecialchars($search_request);
        $this->search_request = $search_request;
    }

    public function search()
    {
        $lang = Router::getLanguage();
        $dbc = Connect::getConnection();
        $indexModel = new IndexModel();
        $material_type_array = $indexModel->getType_of_Materials();
        $data_total = array();

        foreach ($material_type_array as $val) {
            $material_type = $val['type_name'];
            if ($material_type != 'Admin') {

                $fields = new FieldsModel($material_type);
                $fields_list = '';
                $fields_list_value = '';
                foreach ($fields->getFields() as $v) {
                    if ($v != 'id' && $v != 'alias' && $v != 'id_' . $material_type . '') {
                        $fields_list .= ',`'.$v .'` ';
                        $fields_list_value .= $v." LIKE '%".$this->search_request."%' OR ";
                    }
                }
               $fields_list_value = trim($fields_list_value, 'OR ');

                $sql = "SELECT `id`, `id_{$material_type}` {$fields_list}, `alias` FROM `{$material_type}_{$lang}` WHERE {$fields_list_value}";
                //echo $sql;
                $placeholders = array();
                $data = $dbc->getDate($sql, $placeholders);

                $data_total[$material_type] = $data;

            }
        }
        return $data_total;
    }
    public function isSmall()
    {
        return strlen($this->search_request)>3 ? false : true;
    }

    public function isLarge()
    {
        return strlen($this->search_request)>128 ? true : false;
    }

    public function getSearchRequest()
    {
        return $this->search_request;
    }


}