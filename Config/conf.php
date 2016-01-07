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

Config::set('default_rout', 'default');
Config::set('default_language', 'uk');
Config::set('default_controller', 'Index');
Config::set('default_action', 'index');
Config::set('default_id', '1');

// DB Connect

$host = 'test6.ua';
$dbname = 'db_nadc';
$user ='root';
$pass = '';
// user = db_nadc  pass = 3KxJxYAX7QGEzA5E

define('DSN', "mysql:host=$host;dbname=$dbname; charset=UTF8");
define('USER', $user);
define('PASS', $pass);
