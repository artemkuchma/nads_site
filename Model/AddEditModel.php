<?php


class addEditModel
{

    private $title;
    private $new_alias;
    private $translit;
    private $id_parent;
    private $menu_name;
    private $menu_data;
    private $without_menu;
    private $publication;
    private $title_or_menu_name;
    private $material_type;
    private $additional_fields_arr;
    private $additional_fields_value;
    private $additional_fields_value_arr;
    private $additional_fields_key_value;


    public function __construct(Request $request, $material_type)
    {
        $this->title = $request->post('title');
        $this->menu_name = $request->post('menu_name');
        $this->menu_data = $request->post('menu');
        $this->without_menu = $request->post('without_menu');
        $this->publication = $request->post('publication');
        $this->title_or_menu_name = $this->menu_name ? $this->menu_name : $this->title;
        $alias_data = $this->createAlias($this->title_or_menu_name, $this->menu_data);
        $this->new_alias = $alias_data['new_alias'];
        $this->translit = $alias_data['translit'];
        $this->id_parent = $alias_data['id_parent'];
        $this->material_type = $material_type;

        $fields_model = new FieldsModel($material_type);
        $fields = $fields_model->getFields();
        $additional_fields_list = array();
        $additional_fields_value = '';
        $additional_fields_key_value = '';
        $additional_fields_value_arr = array();
        foreach ($fields as $v) {
            if ($v != 'id' && $v != 'alias' && $v != 'id_' . $material_type . '' && $v != 'title') {
                $additional_fields_list[] = $v;
                $additional_fields_value .= ", '" . $request->post($v) . "'";
                $additional_fields_value_arr[$v] = $request->post($v);
                $additional_fields_key_value .= ", `" . $v . "` = '" . $request->post($v) . "'";
            }
        }
        $this->additional_fields_arr = $additional_fields_list;
        $this->additional_fields_value = $additional_fields_value;
        $this->additional_fields_value_arr = $additional_fields_value_arr;
        $this->additional_fields_key_value = $additional_fields_key_value;

    }

    public function isValid()
    {
        return !empty($this->title);
    }

    public function inMenu()
    {
        return empty($this->without_menu);
    }

    public function isMenuName()
    {
        return empty($this->menu_name);
    }

    public function createAlias($title_name, $menu_data)
    {

        $translitClass = new Translit($title_name);
        $translit = $translitClass->translit;


        if ($menu_data) {
            $alias_data_arr = explode('!', $menu_data);
            $alias_parent = $alias_data_arr[0];
            $id_parent = $alias_data_arr[1];
            $slash = '/';
        } else {
            $alias_parent = '';
            $slash = '';
            $id_parent = '';
        }
        $new_alias = $alias_parent . $slash . $translit;
        $alias_data = array(
            'new_alias' => $new_alias,
            'id_parent' => $id_parent,
            'translit' => $translit
        );
        return $alias_data;
    }

    public function isAliasExist($id = null, $language = null)
    {
        $dbc = Connect::getConnection();
        $placeholders = array(
            'new_alias' => $this->new_alias
        );
        $lang = isset($language) ? $language : Config::get('default_language');
        $mat_type = $this->material_type;
        if (!$id) {

            $sql = "SELECT * FROM `{$mat_type}_{$lang}` WHERE alias= :new_alias";
        } else {
            $placeholders = array(
                'new_alias' => $this->new_alias,
                'id' => $id
            );
            $sql = "SELECT * FROM `{$mat_type}_{$lang}` WHERE alias= :new_alias AND  id_{$mat_type} !=
             (SELECT bp_{$lang}.id_{$mat_type} FROM {$mat_type}_{$lang} bp_{$lang} JOIN {$mat_type} bp
             ON bp.id_page = :id AND bp.id = bp_{$lang}.id_{$mat_type})";
        }
        $date = $dbc->getDate($sql, $placeholders);

        return empty($date) ? true : false;
    }


    public function add($with_without_menu = null)
    {

        $publish = $this->publication ? 1 : 0;

        $indexModel = new IndexModel();
        $id_mat_type = '';
        foreach ($indexModel->getType_of_Materials() as $v) {
            if ($v['type_name'] == $this->material_type) {
                $id_mat_type = $v['id'];
            }
        }

        $controller = $this->material_type == 'basic_page' ? 'Index' : ucfirst($this->material_type);

        $placeholders = array(
            'controller' => $controller,
            'action' => 'index',
            'publish' => $publish
        );
        $dbc = Connect::getConnection();
        $sql = "INSERT INTO `pages`(`id_mat_type`, `status`, `controller`, `action`) VALUES ($id_mat_type,:publish,:controller,:action)";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

        $sql = "SELECT MAX(id) AS max_id FROM pages";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
        $id_new_page = $date[0]['max_id'];

        $placeholders = array(
            'id_new_page' => $id_new_page
        );
        $sql = "INSERT INTO `{$this->material_type}`(`id_page`) VALUES (:id_new_page)";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);


        if (!isset($with_without_menu)) {

            $placeholders = array(
                'id_new_page' => $id_new_page,
                'id_parent' => $this->id_parent
            );
            $sql = "INSERT INTO `main_menu`(`id_page`, `id_parent_page`, `status`) VALUES (:id_new_page,:id_parent,1)";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);


            $sql = "SELECT MAX(id) AS max_id FROM main_menu";
            $placeholders = array();
            $date = $dbc->getDate($sql, $placeholders);
            $id_new_menu = $date[0]['max_id'];

            $placeholders = array(
                'id_new_menu' => $id_new_menu,
                'title' => $this->title_or_menu_name,
                'alias' => $this->new_alias
            );
            $lang = Config::get('default_language');

            $sql = "INSERT INTO `main_menu_{$lang}`(`id_main_menu`, `name`, `alias_menu`) VALUES (:id_new_menu,:title,:alias)";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);
// Добавление укр данных в англ. меню - необходимо для нормальной работы меню
            foreach (Config::get('languages') as $v) {
                if ($v != Config::get('default_language')) {
                    $sql = "INSERT INTO `main_menu_{$v}`(`id_main_menu`, `name`, `alias_menu`) VALUES (:id_new_menu,:title,:alias)";
                    $sth = $dbc->getPDO()->prepare($sql);
                    $sth->execute($placeholders);

                }
            }

        }

        $sql = "SELECT MAX(id) AS max_id FROM {$this->material_type}";
        $placeholders = array();
        $date = $dbc->getDate($sql, $placeholders);
        $id_new_page = $date[0]['max_id'];


        $placeholders = array(
            'id_new_page' => $id_new_page,
            'title' => $this->title,
            'alias' => $this->new_alias
        );
        $additional_fields = '';
        foreach ($this->additional_fields_arr as $v) {
            $additional_fields .= ", `$v`";
        }
        $lang = Config::get('default_language');

        $sql = "INSERT INTO {$this->material_type}_{$lang} (`id_{$this->material_type}`,`title`, `alias` $additional_fields)
        VALUES (:id_new_page, :title, :alias $this->additional_fields_value)";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

        //Добавление тайтла и алиаса для англоязычной версии (пока укр вариант  алиаса), необходимо для меню


        $placeholders = array(
            'id_new_page' => $id_new_page,
            'alias' => $this->new_alias
        );
        foreach (Config::get('languages') as $v) {
            if ($v != Config::get('default_language')) {
                $sql = "INSERT INTO {$this->material_type}_{$v} (`id_{$this->material_type}`, `alias`) VALUES (:id_new_page, :alias)";
                $sth = $dbc->getPDO()->prepare($sql);
                $sth->execute($placeholders);
            }
        }


    }

    public function edit($id, $with_without_menu = null)
    {
        $lang = Router::getLanguage();
        $placeholders = array(
            'id' => $id
        );

        $dbc = Connect::getConnection();
        $sql = "SELECT bp_{$lang}.id_{$this->material_type} AS id FROM  {$this->material_type}_{$lang} bp_{$lang} JOIN {$this->material_type} bp ON bp.id = bp_{$lang}.id_{$this->material_type}
        AND bp.id_page = :id";
        $date = $dbc->getDate($sql, $placeholders);
        $id_{$this->material_type} = $date[0]['id'];


        $placeholders = array(
            'title' => $this->title,
            'alias' => $this->new_alias,
            'id_' . $this->material_type => $id_{$this->material_type}
        );
        $sql = "UPDATE `{$this->material_type}_{$lang}` SET `title`= :title,`alias`= :alias $this->additional_fields_key_value WHERE id_{$this->material_type} = :id_{$this->material_type} ";

        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

        $publish = $this->publication ? 1 : 0;
        $placeholders = array(
            'id' => $id,
            'publish' => $publish
        );
        $sql = "UPDATE `pages` SET `status`= :publish WHERE id = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);
         // Проверка - есть ли для этого докумета пункт меню
        $placeholders = array(
            'id' => $id,
        );
        $sql = "SELECT id FROM `main_menu` WHERE id_page = :id ";
        $date = $dbc->getDate($sql, $placeholders);
        $isInMenu = empty($date);
        //Если полученный массив пустой добавляем инф. в меню
        if ($isInMenu) {

            $placeholders = array(
                'id_new_page' => $id,
                'id_parent' => $this->id_parent
            );
            $sql = "INSERT INTO `main_menu`(`id_page`, `id_parent_page`, `status`) VALUES (:id_new_page,:id_parent,1)";
            $sth = $dbc->getPDO()->prepare($sql);
            $sth->execute($placeholders);


            $sql = "SELECT MAX(id) AS max_id FROM main_menu";
            $placeholders = array();
            $date = $dbc->getDate($sql, $placeholders);
            $id_new_menu = $date[0]['max_id'];

            $placeholders = array(
                'id_new_menu' => $id_new_menu,
                'title' => $this->title_or_menu_name,
                'alias' => $this->new_alias
            );
            $lang = Config::get('default_language');


                $sql = "INSERT INTO `main_menu_{$lang}`(`id_main_menu`, `name`, `alias_menu`) VALUES (:id_new_menu,:title,:alias)";
                $sth = $dbc->getPDO()->prepare($sql);
                $sth->execute($placeholders);

            foreach (Config::get('languages') as $v){
                if($v != $lang){

                    $placeholders = array();

                    $sql = "SELECT `alias_menu` FROM main_menu_{$v} WHERE id_main_menu =
                (SELECT id FROM main_menu WHERE id_page = (SELECT id_parent_page FROM main_menu WHERE id_page = {$id}) )";
                    $data = $dbc->getDate($sql, $placeholders);
                    $parent_alias = $data[0]['alias_menu'];


                    $sql = "SELECT n_{$v}.title FROM {$this->material_type}_{$v} n_{$v} JOIN {$this->material_type} n ON n_{$v}.id_{$this->material_type} = n.id AND n.id_page = {$id}";
                    $data = $dbc->getDate($sql, $placeholders);
                    $title = $data[0]['title'];

                    $translitClass = new Translit($title);
                    $translit = $translitClass->translit;
                    $alias_arr = array($parent_alias, $translit);
                    $new_alias = implode('/', $alias_arr);

                    $placeholders = array(
                        'id_new_menu' => $id_new_menu,
                        'title' => $title,
                        'alias' => $new_alias
                    );

                    $sql = "INSERT INTO `main_menu_{$v}`(`id_main_menu`, `name`, `alias_menu`) VALUES (:id_new_menu,:title,:alias)";
                    $sth = $dbc->getPDO()->prepare($sql);
                    $sth->execute($placeholders);
                }
            }

        }


        if (!isset($with_without_menu)) {
            $lang = Config::get('default_language');
            $this->edit_menu($id, $lang, $this->new_alias);


            foreach (Config::get('languages') as $v) {
                if ($v != $lang) {
                    $sql = "SELECT `alias_menu` FROM main_menu_{$v} WHERE id_main_menu =
                (SELECT id FROM main_menu WHERE id_page = (SELECT id_parent_page FROM main_menu WHERE id_page = {$id}) )";
                    $data = $dbc->getDate($sql, $placeholders);
                    $parent_alias = $data[0]['alias_menu'];


                    $sql = "SELECT mm_{$v}.alias_menu, mm_{$v}.name  FROM main_menu_{$v} mm_{$v} JOIN main_menu mm ON mm_{$v}.id_main_menu = mm.id AND mm.id_page = {$id}";
                    $data = $dbc->getDate($sql, $placeholders);
                    $old_alias = $data[0]['alias_menu'];
                    $alias_arr = explode('/', $old_alias);
                    $last_element = array_pop($alias_arr);
                    $alias_arr = array($parent_alias, $last_element);
                    $new_alias = implode('/', $alias_arr);
                    $title = $data[0]['name'];

                    $sql = "UPDATE {$this->material_type}_{$v} bp_{$v} JOIN {$this->material_type} bp SET `alias`= " . '"' . $new_alias . '"' . " WHERE bp_{$v}.id_{$this->material_type} = bp.id AND bp.id_page = {$id} ";
                    $placeholders = array();
                    $sth = $dbc->getPDO()->prepare($sql);
                    $sth->execute($placeholders);


                    $this->edit_menu($id, $v, $new_alias, $title);
                }
            }

        } else {
            $edit = 1;
            $deleteModel = new DeleteModel($id, $edit);

            $deleteModel->delete_from_menu();
        }

    }

    public function translate($id, $lang)
    {
        // Получение нового алиаса по пункту меню

        $placeholders = array(
            'id' => $id
        );
        $dbc = Connect::getConnection();
        $sql = "SELECT `alias_menu` FROM main_menu_{$lang} WHERE id_main_menu =
                (SELECT id FROM main_menu WHERE id_page = (SELECT id_parent_page FROM main_menu WHERE id_page = :id) )";
        $data = $dbc->getDate($sql, $placeholders);
        $parent_alias = $data[0]['alias_menu'];
        //предотвращает появление двойного слеша в алиасах где нет родительского элемента
        $slash = isset($parent_alias) ? '/' : '';


        $translitClass = new Translit($this->title_or_menu_name);
        $translit = $translitClass->translit;

        $new_alias = $parent_alias .$slash. $translit;


        $sql = "SELECT bp_{$lang}.id_{$this->material_type} AS id FROM  {$this->material_type}_{$lang} bp_{$lang} JOIN {$this->material_type} bp ON bp.id = bp_{$lang}.id_{$this->material_type}
        AND bp.id_page = :id";
        $date = $dbc->getDate($sql, $placeholders);
        $id_{$this->material_type} = $date[0]['id'];


        $placeholders = array(
            'title' => $this->title,
            'alias' => $new_alias,
            'id_' . $this->material_type => $id_{$this->material_type}
        );
        $sql = "UPDATE `{$this->material_type}_{$lang}` SET `title`= :title,`alias`= :alias $this->additional_fields_key_value WHERE id_{$this->material_type} = :id_{$this->material_type} ";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

        $placeholders = array(
            'id' => $id
        );
        $sql = "SELECT `id_parent_page` FROM `main_menu` WHERE `id_page`= :id";
        $date = $dbc->getDate($sql, $placeholders);
        $id_parent = $date[0]['id_parent_page'];

        $this->edit_menu($id, 'en', $new_alias, $this->title_or_menu_name, $id_parent );


    }


    private function recurs_update_menu($dbc, $data, $placeholders, $lang)
    {
        // $lang = Router::getLanguage();

        if (count($data)) {
            foreach ($data as $v) {
                $id_t = $v['id_page'];

                $sql = "SELECT `alias_menu` FROM main_menu_{$lang} WHERE id_main_menu =
                (SELECT id FROM main_menu WHERE id_page = (SELECT id_parent_page FROM main_menu WHERE id_page = {$id_t}) )";
                $data = $dbc->getDate($sql, $placeholders);
                $parent_alias = $data[0]['alias_menu'];

                $sql = "SELECT mm_{$lang}.alias_menu FROM main_menu_{$lang} mm_{$lang} JOIN main_menu mm ON mm_{$lang}.id_main_menu = mm.id AND mm.id_page = {$id_t}";
                $data = $dbc->getDate($sql, $placeholders);
                $old_alias = $data[0]['alias_menu'];
                $alias_arr = explode('/', $old_alias);
                $last_element = array_pop($alias_arr);
                $alias_arr = array($parent_alias, $last_element);
                $new_alias = implode('/', $alias_arr);


                $sql = "SELECT id_page FROM `main_menu` WHERE id_parent_page = {$id_t}";
                $data = $dbc->getDate($sql, $placeholders);

                $sql = "UPDATE main_menu_{$lang} mm_{$lang} JOIN main_menu mm SET mm_{$lang}.alias_menu = " . '"' . $new_alias . '"' . "
                WHERE  mm_{$lang}.id_main_menu = mm.id AND mm.id_page = {$id_t}";
                $sth = $dbc->getPDO()->prepare($sql);
                $sth->execute($placeholders);


                $sql = "SELECT `type_name` FROM type_of_materyals tm JOIN pages p ON p.id = {$id_t} AND p.id_mat_type = tm.id";
                $d = $dbc->getDate($sql, $placeholders);
                $material_type = $d[0]['type_name'];


                $sql = "UPDATE {$material_type}_{$lang} bp_{$lang} JOIN {$material_type} bp SET bp_{$lang}.alias = " . '"' . $new_alias . '"' . "
                WHERE  bp_{$lang}.id_{$material_type} = bp.id AND bp.id_page = {$id_t}";
                $sth = $dbc->getPDO()->prepare($sql);
                $sth->execute($placeholders);

                $this->recurs_update_menu($dbc, $data, $placeholders, $lang);
            }
        }

    }

    private function edit_menu($id_page, $lang, $new_alias, $title = null, $id_parent = null)
    {
        $placeholders = array(
            'id' => $id_page
        );
        $dbc = Connect::getConnection();
        $sql = "SELECT id_page FROM `main_menu` WHERE id_parent_page = :id";
        $data = $dbc->getDate($sql, $placeholders);
        // Debugger::PrintR($date);
        $new_title = $title ? $title : $this->title_or_menu_name;
        $id_p = $id_parent ? $id_parent :  $this->id_parent;

        //   $lang = Router::getLanguage();
        $placeholders = array(
            'id' => $id_page,
            'title' => $new_title,
            'alias' => $new_alias,
            'id_parent_page' => $id_p
        );

        $sql = "UPDATE main_menu_{$lang} mm_{$lang} JOIN main_menu mm SET mm_{$lang}.name = :title, mm_{$lang}.alias_menu = :alias, mm.id_parent_page = :id_parent_page
         WHERE mm_{$lang}.id_main_menu = mm.id AND mm.id_page = :id";
        $sth = $dbc->getPDO()->prepare($sql);
        $sth->execute($placeholders);

        $placeholders = array();

        $this->recurs_update_menu($dbc, $data, $placeholders, $lang);
    }


    public function getAlias()
    {
        return $this->new_alias;
    }


    public function getTitle()
    {
        return $this->title;
    }

    public function getMenuName()
    {
        return $this->menu_name;
    }

    public function getMenuData()
    {
        return $this->menu_data;
    }

    public function getWithoutMenu()
    {
        return $this->without_menu;
    }

    public function getPublication()
    {
        return $this->publication;
    }

    public function getIdParent()
    {
        return $this->id_parent;
    }

    public function getNewAlias()
    {
        return $this->new_alias;
    }

    public function getTitleOrMenuName()
    {
        return $this->title_or_menu_name;
    }

    public function getTranslit()
    {
        return $this->translit;
    }

    public function getAdditionalFieldsValueArr()
    {
        return $this->additional_fields_value_arr;
    }


}