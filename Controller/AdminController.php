<?php


class AdminController extends Controller
{
    private $material_type;

    public function indexAction()
    {

        if (Session::hasUser('admin')) {

            $adminModel = new AdminModel();
            $data = $adminModel->getAdminPage(Router::getId());
            $args = $data[0];
            return $this->render_admin($args);
        } else {
            throw new Exception('Access is forbidden', 403);

        }

    }

    public function totalListAction()
    {
        if (Session::hasUser('admin')) {

            $adminModel = new AdminModel();
            $data_admin = $adminModel->getAdminPage(Router::getId());

            $indexModel = new IndexModel();
            $data_materials = $indexModel->getTotalList();

            foreach ($data_materials as $k => $v) {
                if ($v['status'] == 1) {
                    $v['status'] = __t('yes');
                } else {
                    $v['status'] = __t('no');
                }
                $data_materials[$k] = $v;
            }
            $items_count = $indexModel->getCount();
            $items_per_page = Config::get('materials_per_page');

            $request = new Request();
            $currentPage = $request->get('page') ? (int)$request->get('page') : 1;
            $data_pagination = self::getPagination($items_count, $items_per_page, $currentPage);


            if ($items_count) {
                $data_materials_page = array_chunk($data_materials, $items_per_page, true);
                if (isset($data_materials_page[$currentPage - 1])) {
                    $data_materials_page = $data_materials_page[$currentPage - 1];
                } else {
                    throw new Exception('Page (' . Router::getUri() . ') not found', 404);
                }
            } else {
                $data_materials_page = null;
            }

            $data_url = explode('?', Router::getUri());
            $type_of_materials = array();
            foreach ($indexModel->getType_of_Materials() as $v) {
                $type_of_materials[] = strtolower($v['type_name']);
            }

            $system_doc = array(
                '404' => Config::get('default_id_error_404'),
                '403' => Config::get('default_id_error_403'),
                '204' => Config::get('default_id_error_204'),
                '500' => Config::get('default_id_error_500'),
                'default_page' => Config::get('default_id'),
                'not_publish' => Config::get('not_publish')
            );
            $system_doc_rev = array_flip($system_doc);

            $args = array(
                'data_admin' => $data_admin[0],
                'data_materials' => $data_materials_page,
                'data_pagination' => $data_pagination,
                'data_url' => $data_url[0],
                'type_of_materials' => $type_of_materials,
                'system_doc' => $system_doc_rev
            );

            return $this->render_admin($args);
        } else {
            throw new Exception('Access is forbidden', 403);

        }
    }

    public function logListAction()
    {
        if (Session::hasUser('admin')) {

            $adminModel = new AdminModel();
            $data_admin = $adminModel->getAdminPage(Router::getId());
            $data = $adminModel->getLogPage();

            $data_array = explode('///', $data);

            foreach ($data_array as $k => $v) {
                $data_array[$k] = explode('/./', $v);
            }
            array_pop($data_array);


            $items_count = count($data_array);
            $items_per_page = Config::get('log_per_page');

            $request = new Request();
            $currentPage = $request->get('page') ? (int)$request->get('page') : 1;
            $data_pagination = self::getPagination($items_count, $items_per_page, $currentPage);

            if ($items_count) {
                $data_log_page = array_chunk($data_array, $items_per_page, true);
                if (isset($data_log_page[$currentPage - 1])) {
                    $data_log_page = $data_log_page[$currentPage - 1];
                } else {
                    throw new Exception('Page (' . Router::getUri() . ') not found', 404);
                }
            } else {
                $data_log_page = null;
            }
            $data_url = explode('?', Router::getUri());


            $args = array(
                'data_admin' => $data_admin[0],
                'data_materials' => $data_log_page,
                'data_pagination' => $data_pagination,
                'data_url' => $data_url[0]
            );
            return $this->render_admin($args);
        } else {
            throw new Exception('Access is forbidden', 403);

        }


    }

    public function cleanLogFileAction()
    {
        if (Session::hasUser('admin')) {
            $file_path = WEBROOT_DIR . 'log.txt';
            self::rewrite_file($file_path, 'w', '');
            self::redirect('/admin/zurnal_oshibok');
        } else {
            throw new Exception('Access is forbidden', 403);

        }
    }

    public function listStaticTranslationAction()
    {
        if (Session::hasUser('admin')) {
            $adminModel = new AdminModel();
            $data_admin = $adminModel->getAdminPage(Router::getId());
            $data_translation = $adminModel->getStaticTranslation();

            $items_count = count($data_translation);
            $items_per_page = Config::get('translation_per_page');

            $request = new Request();
            $currentPage = $request->get('page') ? (int)$request->get('page') : 1;
            $data_pagination = self::getPagination($items_count, $items_per_page, $currentPage);

            if ($items_count) {
                $data_translation_page = array_chunk($data_translation, $items_per_page, true);
                if (isset($data_translation_page[$currentPage - 1])) {
                    $data_translation_page = $data_translation_page[$currentPage - 1];
                } else {
                    throw new Exception('Page (' . Router::getUri() . ') not found', 404);
                }
            } else {
                $data_translation_page = null;
            }

            $data_url = explode('?', Router::getUri());

            $args = array(
                'data_admin' => $data_admin[0],
                'data_materials' => $data_translation_page,
                'data_pagination' => $data_pagination,
                'data_url' => $data_url[0]
            );
            return $this->render_admin($args);

        } else {
            throw new Exception('Access is forbidden', 403);

        }

    }

    public function addBasicPageAction()
    {
        $this->material_type = 'basic_page';
       return $this->addAction();
    }

    public function addNewsAction()
    {
        $this->material_type = 'news';
        return $this->addAction();
    }

    public function addAction()
    {
        if (Session::hasUser('admin')) {

            $adminModel = new AdminModel();
            $data_admin = $adminModel->getAdminPage(Router::getId());

            $request = new Request();
            $addModel = new AddEditModel($request, $this->material_type);

            $menuModel = new MenuModel();
            $data = $menuModel->getMainMenu('uk');
            $menuController = new MenuController();
            $main_menu_array = $menuController->menuArray($data);

            if ($request->isPost()) {
                if ($addModel->isValid()) {
                    if ($addModel->isAliasExist()) {
                        if ($addModel->inMenu()) {

                            $addModel->add();

                        } else {
                            $with_without_menu = 1;
                            $addModel->add($with_without_menu);
                        }
                    } else {
                        Session::setFlash('Документ с таким псевдонимом уже существует!');
                    }
                } else {
                    Session::setFlash('Поле "Заголовок" обязательно для заполнения');
                }
            }
            $this->rewrite_file_alias();
            $args = array(
                'data_admin' => $data_admin[0],
                'addModel' => $addModel,
                'data_menu' => $main_menu_array,
                'redirect' => $request->post('redirect')
            );
            $tpl = 'add'.str_replace(' ', '', ucwords(str_replace('_', ' ', $this->material_type)));

            return $this->render_admin($args, $tpl);

        } else {
            throw new Exception('Access  denied', 403);
        }
    }


    public function deleteAction()
    {
        if (Session::hasUser('admin')) {

            $deleteModel = new DeleteModel(Router::getId());
            $deleteModel->delete();

            $this->rewrite_file_alias();

            $this->redirect('/' . Router::get_alis_by_id(94, 'uk'));

        } else {
            throw new Exception('Access  denied', 403);
        }
    }

    public function editBasicPageAction()
    {
        $this->material_type = 'basic_page';
        return $this->editAction();
    }

    public function editNewsAction()
    {
        $this->material_type = 'news';
        return $this->editAction();
    }

    public function editAction()
    {
        if (Session::hasUser('admin')) {

            $indexModel = new IndexModel();
            $data_page = $indexModel->getPage(Router::getId(), Router::getLanguage(), $this->material_type);

            $menuModel = new MenuModel();
            $data = $menuModel->getMainMenu('uk');
            $menuController = new MenuController();
            $main_menu_array = $menuController->menuArray($data);
            $data_menu_item = $menuModel->getMenuDatePage($data_page[0]['id']);
           // Debugger::PrintR($main_menu_array);


            $request = new Request();
            $editModel = new AddEditModel($request, $this->material_type);
            if ($request->isPost()) {
                if ($editModel->isValid()) {
                    if ($editModel->isAliasExist($data_page[0]['id'])) {
                        if ($editModel->inMenu()) {

                            $editModel->edit($data_page[0]['id']);

                        } else {
                            $with_without_menu = 1;
                            $editModel->edit($data_page[0]['id'],$with_without_menu);
                        }
                    } else {
                        Session::setFlash('Документ с таким псевдонимом уже существует!');
                    }
                } else {
                    Session::setFlash('Поле "Заголовок" обязательно для заполнения');
                }
            }
            $this->rewrite_file_alias();
          //  Debugger::PrintR($data_page);


            $args = array(
                'data_page' => $data_page,
                'data_menu' => $main_menu_array,
                'edit_model' => $editModel,
                'data_menu_item' => $data_menu_item,
                'redirect' => $request->post('redirect')
            );

            $tpl = 'edit'.str_replace(' ', '', ucwords(str_replace('_', ' ', $this->material_type)));

            return $this->render_admin($args, $tpl);
        } else {
            throw new Exception('Access  denied', 403);
        }

    }

    public function translateBasicPageAction()
    {
        $this->material_type = 'basic_page';
        return $this->translateAction();
    }

    public function translateNewsAction()
    {
        $this->material_type = 'news';
        return $this->translateAction();
    }
    public function translateAction()
    {
        if (Session::hasUser('admin')) {


            $request = new Request();
            $addModel = new AddEditModel($request, $this->material_type);

            $menuModel = new MenuModel();
            $data = $menuModel->getMainMenu('en');
            $menuController = new MenuController();
            $main_menu_array = $menuController->menuArray($data);

            if ($request->isPost()) {
                if ($addModel->isValid()) {
                    if ($addModel->isAliasExist()) {
                        if ($addModel->inMenu()) {

                            $addModel->add();

                        } else {
                            $with_without_menu = 1;
                            $addModel->add($with_without_menu);
                        }
                    } else {
                        Session::setFlash('Документ с таким псевдонимом уже существует!');
                    }
                } else {
                    Session::setFlash('Поле "Заголовок" обязательно для заполнения');
                }
            }
            $this->rewrite_file_alias();
            $args = array(

                'addModel' => $addModel,
                'data_menu' => $main_menu_array,
                'redirect' => $request->post('redirect')
            );
            $tpl = 'translate'.str_replace(' ', '', ucwords(str_replace('_', ' ', $this->material_type)));

            return $this->render_admin($args, $tpl);

        } else {
            throw new Exception('Access  denied', 403);
        }
    }


}