<?php

/* DEFINE PATHS ---------------------------------------------------------- */

// Directory splitter
define('DS', DIRECTORY_SEPARATOR);

// Path to application (full path)
define('APP',dirname(__FILE__) . DS);

// Path to application config (full path)
define('CONFIG',APP . 'config' . DS);

// Path to application controllers (full path)
define('MODELS',APP . 'models' . DS);

// Path to application views (full path)
define('VIEWS',APP . 'views' . DS);

// Path to application controllers (full path)
define('CONTROLLERS',APP . 'controllers' . DS);

// Path to libraries (full path)
define('LIB',dirname(APP) . DS . 'lib' . DS);

// Website document root (full path)
define('HTTPDOCS',$_SERVER['DOCUMENT_ROOT'] . DS);

// Server/domain name with http(s)://
define('WWW','http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['SERVER_NAME'] . '/');


/* LOAD REQUIRED FILES --------------------------------------------------- */

// Environment config
require_once CONFIG . 'environment.php';

// Core MVC framework classes
require_once LIB . 'Brickhouse' . DS . 'ErrorHandler.php';
require_once LIB . 'Brickhouse' . DS . 'Model.php';
require_once LIB . 'Brickhouse' . DS . 'ControllerFront.php';
require_once LIB . 'Brickhouse' . DS . 'Controller.php';

// Database classes
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'Db.interface.php';
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'AdaptorMysql.class.php';


/* LOAD PLUGINS ---------------------------------------------------------- */
require_once LIB . 'Brickhouse' . DS . 'plugins' . DS . 'sitemap.php';
//require_once ROOT . APP . 'plugins' . DS . 'pre_render.php';


/* CONTROLLER/ROUTER ----------------------------------------------------- */
//error handling
//$error = ErrorHandler::getInstance();
set_error_handler(array("ErrorHandler","capture"));


// Only connect to the database if it is configured
if (isset($GLOBALS['DATABASE'])) {
	$db = AdaptorMysql::getInstance();
}

// Controller
$controller = ControllerFront::getInstance();

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
