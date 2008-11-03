<?php

class ControllerFront
{

	protected static $instance = null;
	protected static $requestUri;
	protected static $requestA;

	private function __construct()
	{
		self::setUri();
	}
	
	public static function setUri()
	{
		//mix and match, gets the job done, should be cleaned up
		
		self::$requestUri = ($qsa = strpos($_SERVER['REQUEST_URI'],'?')) ? substr($_SERVER['REQUEST_URI'],0,$qsa) : $_SERVER['REQUEST_URI'];
		self::$requestA = array_slice(explode('/',self::$requestUri),1);
		
		
		//setConfig("CMS_ROOT",substr($_SERVER['PHP_SELF'],0,-strlen('index.php')));
		
		//script name = last item in array
		$a = explode("/",$_SERVER['PHP_SELF']);
		$t = $a[count($a)-1];
		
		/*
		if(isset($_SERVER["HTTP_REFERER"])){
			$this->refA = explode("/",$_SERVER["HTTP_REFERER"]);
			$t = array_search($_SERVER['SERVER_NAME'],$this->refA);
			$this->refA = array_slice($this->refA, ($t+1));
		}
		*/
		
		$tA = explode("/",substr($_SERVER['PHP_SELF'],1,-(strlen($t) + 1)));
		define('BASE',substr($_SERVER['PHP_SELF'],0,-strlen($t)));
		if($tA[0] != ''){
			array_splice(self::$requestA,0,count($tA));
			/*
			if(isset($this->refA)){
				$this->refA = array_slice($this->refA,count($tA)); 
			}
			*/
		}
		
		self::$requestUri = '/' . join('/',self::$requestA);
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
	
	public static function getRoute($custom_routes=array())
	{
		// Default routes (use these if they are not overridden in the router.php file)
		$default_routes = array();
		// Default homepage (index)
		if (!isset($custom_routes['default_home'])) $default_routes['default_home'] = array('uri' => "/^\/$/");
		// Default controller
		if (!isset($custom_routes['default_controller'])) $default_routes['default_controller'] = array('uri' => "/^\/(?P<controller>[a-z0-9_-]+)$/i");
		// Default controller+action
		if (!isset($custom_routes['default_controller_action'])) $default_routes['default_controller_action'] = array('uri' => "/^\/(?P<controller>[a-z0-9_-]+)\/(?P<action>[a-z0-9_-]+)$/i");
		
		// Combine default and custom routes
		$routes = array_merge($custom_routes,$default_routes);
		
		// Try to find a match
		foreach ($routes as $route) {
			if (preg_match($route['uri'],self::$requestUri,$regs)) {
				// Make sure match is exact
				if ($regs[0] == self::$requestUri) {
					// Set key 'uri' with uri match
					$regs['uri'] = $regs[0];
					// Make sure controller is set, or use DEFAULT_CONTROLLER
					if (!isset($regs['controller'])) {
						$regs['controller'] = isset($route['controller']) ? $route['controller'] : DEFAULT_CONTROLLER;
					}
					// Make sure action is set, or use DEFAULT_ACTION
					if (!isset($regs['action'])) {
						$regs['action'] = isset($route['action']) ? $route['action'] : DEFAULT_ACTION;
					}
					// Only return a found route if the controller exists!
					if (file_exists(CONTROLLERS . ucfirst($regs['controller']) . 'Controller.php')) {
						return $regs;
					}
				}
			}
		}
	}


	//needs to do the dispatching here so we can grab entire output and filter with a pre and post render plugin
	public static function dispatch($routes)
	{
		
		$output = '';
		
		// Find a matching route
		if ($route = self::getRoute($routes)) {
			//format the file name of the controller - camel case, append Controlller
			$name = ucfirst($route['controller']) . 'Controller';
			$file = CONTROLLERS . $name . '.php';
			
			if (file_exists($file) && require_once $file) {
				
				//action
				$action = DEFAULT_ACTION;
				if(isset($route['action'])){
					$action = $route['action'];
				}
				
				$action = ucfirst($action);
				$controller = new $name($route);
				
				//could force index to always exist by using an interface or abstract class
				//however, this needs to be here to catch human error in a route
				if (method_exists($controller,$action)) {
					$controller->$action();
					$output .= $controller->render();
					
					if (function_exists('plugin__pre_render')) {
						$output = plugin__pre_render($output);
					}
					
					print $output;
					
					if (function_exists('plugin__post_render')) {
						plugin__post_render($output);
					}
					
					//stop matching
					return;
					
				} else {
					//action doesn't exist
					//self::$error->code = 404;
					ErrorHandler::message($action.' action doesn\'t exist in '.$name);
				}
				
			} else {
				//self::$error->code = 404;
				ErrorHandler::message($name.' controller doesn\'t exist');
			}
			
		} else {
			//self::$error->code = 404;
			//ErrorHandler::message('Router did not match any controllers');
			ErrorHandler::message('No valid route found!');
		}
		
	}

}
