<?php

/* DEFINE PATHS ---------------------------------------------------------- */

// Directory splitter
define('DS', DIRECTORY_SEPARATOR);

// Site/app root (full path)
define('ROOT',$_SERVER['DOCUMENT_ROOT'] . DS);

// Path to application (relative to index.php)
define('APP',basename(dirname(__FILE__)) . DS);

// Path to application config (full path)
define('CONFIG',ROOT . APP . 'config' . DS);

// Path to application controllers (full path)
define('MODELS',ROOT . APP . 'models' . DS);

// Path to application views (full path)
define('VIEWS',ROOT. APP . 'views' . DS);

// Path to application controllers (full path)
define('CONTROLLERS',ROOT . APP . 'controllers' . DS);

// Path to libraries - relative to index.php
define('LIB','lib' . DS);

// Web root ??? what is this for ???
define('WEB_ROOT','');

// Server/domain name with http(s)://
define('WWW','http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/');


/* LOAD REQUIRED FILES --------------------------------------------------- */

// Environment config
require_once CONFIG . 'environment.php';

// Core MVC framework classes
require_once LIB . 'BrickHouse' . DS . 'ErrorHandler.php';
require_once LIB . 'BrickHouse' . DS . 'Model.php';
require_once LIB . 'BrickHouse' . DS . 'ControllerFront.php';
require_once LIB . 'BrickHouse' . DS . 'Controller.php';

// Database classes
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'Db.interface.php';
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'AdaptorMysql.class.php';


/* LOAD PLUGINS ---------------------------------------------------------- */
require_once LIB . 'BrickHouse' . DS . 'plugins' . DS . 'sitemap.php';
//require_once ROOT . APP . 'plugins' . DS . 'pre_render.php';


/* CONTROLLER/ROUTER ----------------------------------------------------- */
//error handling
//$error = ErrorHandler::getInstance();
set_error_handler(array("ErrorHandler","capture"));


// Only connect to the database if it is configured
if (isset($GLOBALS['DATABASE'])) {
	$db = new AdaptorMysql();
	$db->sql('SET NAMES utf8');
}

// Controller
$controller = ControllerFront::getInstance();
//if (isset($db)) $controller->setDb($db);

// Router
//needs to follow the Front Controller so we can utilize the pre-parsed URI
$uA = $controller->getUri();
$uri = $uA['array'];

$routes = array();

//pull in predefined routes
require_once CONFIG . 'routes.php';

//run sitemap plugin
$q = plugin__sitemap(array('uri'=>$uri,'table'=>'pages','parent_id'=>'parent_id','slug'=>'slug','db'=>$db));

if(is_array($q)){
	//overrite existing route to prevent error, could be written simpler if using pattern as array key, later perhaps
	for($i=0;$i<count($routes);$i++){
		if($router[$i]['route'] == "^" . $uA['string'] . "$"){
			array_splice($routes,$i,1);
		}
	}
	//this is custom for this instance, so use controller and action info from the db, not the config
	$routes[] = array('route'=>"^" . $uA['string'] . "$",'controller'=>$q['controller'],'action'=>$q['action']);
}


/* DISPATCH -------------------------------------------------------------- */

$controller->dispatch($routes);
