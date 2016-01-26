<?php


class AdminController extends Controller
{


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
                }else{
                    throw new Exception('Page ('.Router::getUri() .') not found', 404);
                }
            } else {
                $data_materials_page = null;
            }

            $data_url = explode('?', Router::getUri());

            $args = array(
                'data_admin' => $data_admin[0],
                'data_materials' => $data_materials_page,
                'data_pagination' => $data_pagination,
                'data_url' => $data_url[0]
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
                }else{
                    throw new Exception('Page ('.Router::getUri() .') not found', 404);
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
                }else{
                    throw new Exception('Page ('.Router::getUri() .') not found', 404);
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
        if (Session::hasUser('admin')) {
            $adminModel = new AdminModel();
            $data_admin = $adminModel->getAdminPage(Router::getId());

           $request = new Request();
            $addModel = new addModel($request);

            $menuModel = new MenuModel();
            $data = $menuModel->getMainMenu('uk');
            $menuController = new MenuController();
            $main_menu_array = $menuController->menuArray($data);

           // Debugger::PrintR($main_menu_array);
            if($request->isPost()){
                if($addModel->isValid()){
                    if($addModel->inMenu()){


                            $addModel->addBasicPage();//($addModel->getTitle(), $addModel->getMenuData(), $addModel->getPublication(),$addModel->getText());



                    }else{
                        $with_without_menu = 1;
                        $addModel->addBasicPage($with_without_menu);
                    }

                }else{
                    Session::setFlash('Поле "Заголовок" обязательно для заполнения');
                }
            }
            $this->rewrite_file_alias();






            $args = array(
                'data_admin' => $data_admin[0],
                'addModel' => $addModel,
                'data_menu' => $main_menu_array
            );

            return $this->render_admin($args);

        }else{
            throw new Exception('Access  denied', 403);
        }

    }


}