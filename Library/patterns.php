<?php
$url_patterns = array(

    'edit_page' => array(
        'pattern_uk' => 'redaguvati/.*',
        'pattern_en' => 'edit/.*',
        'action' => 'edit',
        'elements_before_alias' => 1
    ),

    'edd_to_cart' => array(
        'pattern_uk' => 'koshik/dodatu/.*',
        'pattern_en' => 'cart/add/.*',
        'action' => 'addToCart',
        'elements_before_alias' => 2
    ),

    'show_cart' => array(
        'pattern_uk' => 'koshik/.*',
        'pattern_en' => 'cart/.*',
        'action' => 'showCart',
        'elements_before_alias' => 1
    ),

    'remove_from_cart' => array(
        'pattern_uk' => 'koshik/vudalutu/.*',
        'pattern_en' => 'cart/remove/.*',
        'action' => 'removeFromCart',
        'elements_before_alias' => 2
    ),

    'register' => array(
        'pattern_uk' => 'registrachiya',
        'pattern_en' => 'register',
        'controller' => 'security',
        'action' => 'register',
        'elements_before_alias' => 1
    ),

    'login' => array(
        'pattern_uk' => 'uviiti',
        'pattern_en' => 'login',
        'controller' => 'security',
        'action' => 'login',
        'elements_before_alias' => 1
    ),
    'logout' => array(
        'pattern_uk' => 'vuhid',
        'pattern_en' => 'logout',
        'controller' => 'security',
        'action' => 'logout',
        'elements_before_alias' => 1
    ),

    /**
     * также можно добавлять сюда и контроллеры. Контроллеры и экшены прописанные в шаблонах перепишут
     * контроллеры и экшены из массивов с алиасами.
     */
    /**
    'error_log' => array(
    'pattern' => 'error_log',
    'action' => 'error',
    'elements_before_alias' => 1

    )
     **/
);
