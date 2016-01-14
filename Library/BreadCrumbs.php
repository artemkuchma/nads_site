<?php


class BreadCrumbs {

    public static function getBreadcrumbs()
    {
        return Router::getUri();//MenuController::getMainMenuArray();
    }

}