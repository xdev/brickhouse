<?php

class IndexController extends _Controller
{
	
	//public function __construct($route)
	//{
	//	parent::__construct($route);
	//}
	
	public function Index()
	{
		// Set layout stuff
		$data['htmlTitle'] = 'Brickhouse MVC Framework';
		$this->layout(array('view'=>'master','data'=>$data));
		unset($data);
		
		// Set view stuff
		$data['test'] = 'Controller data';
		$this->view(array('container'=>'main','view'=>'index','data' => $data));
	}
	
}
