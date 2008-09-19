<?php

class Model
{	
	protected $db;
	protected $route;
	
	public function __construct($route)
	{
		$this->route = $route;
		$this->db = AdaptorMysql::getInstance();
	}
	
}
