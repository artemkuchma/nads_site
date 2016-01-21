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

}