<?php

class Controller
{

	protected $route;
	protected $front;
	protected $model;
	protected $layout_view;
	protected $layout_data;
	protected $layout_file;
	protected $output;
	protected $view_data;
		
	function __construct($route,$loadmodel=true)
	{
		$this->route = $route;
		$this->view = $this->route['action'];
		if($loadmodel){
			$this->model = $this->loadModel();
		}
		
		// Init layout defaults
		$this->layout_view = DEFAULT_LAYOUT;
		$this->layout_data = array();
		$this->layout_file = VIEWS . '_layouts' . DS . $this->layout_view . '.' . VIEW_EXTENSION;
		
		$this->output = array();
	}
	
	
	
	/**
	 * Finds the proper view file
	 *
	 * @return void
	 * @author Joshua Rudd
	 **/
	private function getViewFile($view,$folder=null)
	{
		// Set folder to controller unless specified otherwise (typically for '_layouts')
		$folder = $folder ? $folder : $this->route['controller'];
		
		// If view includes a file extension, use it - otherwise use default file extension
		$extension = strrpos($view,'.') ? '' : '.' . VIEW_EXTENSION;
		
		// If the view starts with a slash, start from the top-level view directory
		if (substr($view,0,1) == DS) {
			$file = VIEWS . substr($view,1) . $extension;
		}
		// Otherwise load the controller-based view
		else {
			$file = VIEWS . $folder . DS . $view . $extension;
		}
		return $file;
	}
	
	
	
	/**
	 * Preps and stacks a view for rendering
	 *
	 * @return void
	 * @author Joshua Rudd
	 **/
	protected function view($args=null)
	{
		// Check/set variables
		$view_container = isset($args['container']) ? $args['container'] : DEFAULT_CONTAINER;
		$view_view      = isset($args['view'])      ? $args['view']      : $this->route['action'];
		$view_data      = isset($args['data'])      ? $args['data']      : null;
		
		// Set view file
		$view_file = $this->getViewFile($view_view);
		
		// Create variables for view from data array
		if ($view_data) foreach ($view_data as $dataKey => $dataValue) {
			${$dataKey} = $dataValue;
		}
		
		// Fetch view and store it for output
		ob_start();
		if (@include $view_file) {
			if (!isset($this->output[$view_container])) $this->output[$view_container] = '';
			$this->output[$view_container] .= ob_get_contents();
		} else {
			ErrorHandler::message($view_file . '.' . VIEW_EXTENSION . ' not found!');
		}
		ob_end_clean();
	}
	
	
	
	/**
	 * Configures a layout (optional)
	 *
	 * @return void
	 * @author Joshua Rudd
	 **/
	protected function layout($args=null)
	{
		// Check/set variables
		$this->layout_view = isset($args['view']) ? $args['view'] : $this->layout_view;
		$this->layout_data = isset($args['data']) ? $args['data'] : $this->layout_data;
		
		// Set layout file
		$this->layout_file = $this->getViewFile($this->layout_view,'_layouts');
	}
	
	
	
	/**
	 * Fetches view for inclusion in controller data or within another view
	 *
	 * @return string
	 * @author Joshua Rudd
	 **/
	protected function fetchView($view_view,$view_data=null)
	{
		// Set view file
		$view_file = $this->getViewFile($view_view);
		
		// Create variables for view from data array
		if ($view_data) foreach ($view_data as $dataKey => $dataValue) {
			${$dataKey} = $dataValue;
		}
		
		// Include view snippet
		ob_start();
		if (!@include $view_file) {
			ErrorHandler::message($view_file . ' not found!');
		}
		$r = ob_get_contents();
		ob_end_clean();
		
		return $r;
	}
	
	
	
	/**
	 * Combines layout and view(s) and prints final output
	 *
	 * @return string
	 * @author Charles Mastin
	 * @author Joshua Rudd
	 **/
	public function render()
	{
		$r = null;
		
		// Create variables for layout from layout_data array
		if ($this->layout_data) foreach ($this->layout_data as $dataKey => $dataValue) {
			${$dataKey} = $dataValue;
		}
		
		// Create content variables from container output array
		$content = array();
		if ($this->output) foreach ($this->output as $dataKey => $dataValue) {
			$content[$dataKey] = $dataValue;
		}
		
		// Capture output of layout/view and then print it
		ob_start();
		// If no layout is set, just spit out the view(s)
		if (!$this->layout_view) {
			$r .= $content['main'];
		}
		// If a layout is found, fetch and return it
		elseif ($this->layout_view && @include $this->layout_file) {
			$r .= ob_get_contents();
		}
		// If the layout isn't found, generate an error
		else {
			//ErrorHandler::code = 404;
			ErrorHandler::message('The ' . $this->layout_view . '.tpl layout could not be found!');
		}
		ob_end_clean();
		
		return $r;
	}
	
	
	
	/**
	 * Loads a model and passes a new instance
	 *
	 * @return class
	 * @author Charles Mastin
	 * @author Joshua Rudd
	 **/
	public function loadModel($model=null)
	{
		// Use either the default model (same as Controller name) or the one that is passed
		$model ? true : $model = ucfirst($this->route['controller']);
		$file = MODELS . $model . 'Model.php';
		
		//auto load that son
		if (file_exists($file)) {
			require_once $file;
			$className = $model . 'Model';
			return new $className($this->route);
			//$this->model = call_user_func($c . '::getInstance');
		}
	}
	
}
