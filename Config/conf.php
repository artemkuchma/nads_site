<?php
define('DS',DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)) . DS);
define('CONF_DIR', ROOT . 'Config' . DS);
define('CONTROLLER_DIR', ROOT . 'Controller' . DS);
define('LANG_DIR', ROOT . 'Language' . DS);
define('LIB_DIR', ROOT . 'Library' . DS);
define('MODEL_DIR', ROOT . 'Model' . DS);
define('VIEW_DIR', ROOT . 'View' . DS);
define('WEBROOT_DIR', ROOT . 'Webroot' . DS);

Config::set('site_name', 'Антидопинговый центр');
Config::set('languages', array('en', 'uk'));
//Config::set('routs', array(
  //  'default'=>'',
   // 'admin'=>'admin_'
//));
$request = new Request();


Config::set('default_rout', 'default');
Config::set('default_language', 'uk');
Config::set('default_controller', 'Index');
Config::set('default_action', 'index');
Config::set('default_id', 7);
Config::set('default_id_error_404', 13);
Config::set('default_id_error_403', 14);
Config::set('default_id_error_204', 15);
Config::set('default_id_error_500', 16);
Config::set('not_publish', 136);
Config::set('admin_basic_page', 20);
Config::set('admin_news', 21);
Config::set('contacts', 216);
Config::set('news', 218);
Config::set('search', 224);
Config::set('bread_crumbs_last_element_view', 'yes');//текущий элемент в бредкрамбсов - показывать -'yes', не показывать - 'no'
Config::set('materials_per_page',$request->get('materials_per_page') ? $request->get('materials_per_page'):10 );
Config::set('translation_per_page',$request->get('translation_per_page') ? $request->get('translation_per_page'):10 );
Config::set('message_per_page',$request->get('message_per_page') ? $request->get('message_per_page'):10 );
Config::set('log_per_page',$request->get('log_per_page') ? $request->get('log_per_page'):10 );
Config::set('news_per_page',$request->get('news_per_page') ? $request->get('news_per_page'):10 );
Config::set('search_per_page',$request->get('search_per_page') ? $request->get('search_per_page'):10 );
Config::set('admin_email', 'test@test6.ua');

// DB Connect

$host = 'test6.ua';
$dbname = 'db_nadc';
$user ='root';
$pass = '';
// user = db_nadc  pass = 3KxJxYAX7QGEzA5E

define('DSN', "mysql:host=$host;dbname=$dbname; charset=UTF8");
define('USER', $user);
define('PASS', $pass);
