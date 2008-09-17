<?php

class ControllerFront
{

	private static $instance = null;
	private static $requestUri;
	private static $requestA;
	private static $db;

	private function __construct()
	{
		self::$requestUri = ($qsa = strpos($_SERVER['REQUEST_URI'],'?')) ? substr($_SERVER['REQUEST_URI'],0,$qsa) : $_SERVER['REQUEST_URI'];
		self::$requestA = array_slice(explode('/',self::$requestUri),1);
	}

	public static function setDb($database)
	{
		self::$db = $database;
	}

	public static function getInstance()
	{
		if(!self::$instance){
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}

	public static function getUri()
	{
		return array('string'=>self::$requestUri,'array'=>self::$requestA);
	}


	//needs to do the dispatching here so we can grab entire output and filter with a pre and post render plugin
	public static function dispatch($router)
	{

		$output = '';

		foreach($router as $route){
			if(ereg($route['route'],self::$requestUri,$regs)){

				//format the file name of the controller - camel case, append Controlller
				$name = ucfirst($route['controller']) . 'Controller';
				$file = CONTROLLERS . $name . '.php';

				if(file_exists($file) && require_once $file){

					//action
					$action = DEFAULT_ACTION;
					if(isset($route['action'])){
						$action = $route['action'];
					}

					$action = ucfirst($action);
					$controller = new $name($route,self::$db);

					//could force index to always exist by using an interface or abstract class
					//however, this needs to be here to catch human error in a route
					if(method_exists($controller,$action)){
						$controller->$action();
						$output .= $controller->render();

						if(function_exists('plugin__pre_render')){
							$output = plugin__pre_render($output);
						}                  

						print $output;

						if(function_exists('plugin__post_render')){
							plugin__post_render($output);
						}

						//stop matching
						return;

					}else{
						//action doesn't exist
						//self::$error->code = 404;
						ErrorHandler::message($action.' action doesn\'t exist in '.$name);
					}

				}else{ 

					//self::$error->code = 404;
					ErrorHandler::message($name.' controller doesn\'t exist');
				}


			} else {
				//self::$error->code = 404;
				//ErrorHandler::message('Router did not match any controllers');
			}
		}
		//no matches found at all - fail!
		ErrorHandler::message('No valid route found!');

	}

}
