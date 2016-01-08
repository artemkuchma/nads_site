<?php

$url_patterns = array(

    'edit_page'=>array(
        'pattern' => 'edit/.*',
        'action' => 'edit',
        'elements_before_alias' => 1
    ),

    'edd_to_cart' => array(
        'pattern' => 'cart/add/.*',
        'action' => 'addToCart',
        'elements_before_alias' => 2
    ),

    'show_cart' => array(
        'pattern' => 'cart/.*',
        'action' => 'showCart',
        'elements_before_alias' => 1
    ),

    'remove_from_cart' => array(
        'pattern' => 'cart/remove/.*',
        'action' => 'removeFromCart',
        'elements_before_alias' => 2
    )
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


$url_alias_uk = array(

    'stranitsa_1' => array(
        'controller' => 'Index',
        'action' => 'index',
        'id' => 1
    ),

    'error_404' => array(
        'controller' => 'Index',
        'action' => 'index',
        'id' => 5
    ),

    'test' => array(
    'controller' => 'Index',
    'action' => 'test',
    'id' => 6
)
);

$url_alias_en = array(

    'page_1' => array(
        'controller' => 'Index',
        'action' => 'index',
        'id' => 1
    ),

    'error_404' => array(
        'controller' => 'Index',
        'action' => 'index',
        'id' => 5
    )



);