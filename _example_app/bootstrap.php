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

// APP's custom MVC classes
require_once MODELS      . '_Model.php';
require_once CONTROLLERS . '_ControllerFront.php';
require_once CONTROLLERS . '_Controller.php';

// Database classes
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'Db.interface.php';
require_once LIB . 'Bobolink' . DS . 'database' . DS . 'AdaptorMysql.class.php';

// Bobolink classes (load any you want to use)
//require_once LIB . 'Bobolink' . DS . 'email'    . DS . 'EmailProtector.class.php';
//require_once LIB . 'Bobolink' . DS . 'forms'    . DS . 'Forms.class.php';
//require_once LIB . 'Bobolink' . DS . 'session'  . DS . 'Session.class.php';
//require_once LIB . 'Bobolink' . DS . 'utils'    . DS . 'Utils.class.php';
//require_once LIB . 'Bobolink' . DS . 'xml'      . DS . 'FlashMW.class.php';
//require_once LIB . 'Bobolink' . DS . 'xml'      . DS . 'XmlToArray.class.php';


/* LOAD PLUGINS ---------------------------------------------------------- */

require_once APP . 'plugins' . DS . 'pre_render.php';
require_once APP . 'plugins' . DS . 'post_render.php';
//require_once LIB . 'Brickhouse' . DS . 'plugins' . DS . 'sitemap.php';


/* CONTROLLER/ROUTER ----------------------------------------------------- */

//error handling
//$error = ErrorHandler::getInstance();
set_error_handler(array("ErrorHandler","capture"));


// Only connect to the database if it is configured
if (isset($GLOBALS['DATABASE'])) {
	$db = AdaptorMysql::getInstance();
}

// Controller
$controller = _ControllerFront::getInstance();

// Router
//needs to follow the Front Controller so we can utilize the pre-parsed URI
$uA = $controller->getUri();
$uri = $uA['array'];

$routes = array();

//pull in predefined routes
require_once CONFIG . 'routes.php';


/* DISPATCH -------------------------------------------------------------- */

$controller->dispatch($routes);
