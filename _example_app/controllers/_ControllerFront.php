<?php

class _ControllerFront extends ControllerFront
{
	
	private function __construct()
	{
		self::setUri();
	}
	
	//override the singleton constructor
	public static function getInstance()
	{
		if(!self::$instance){
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}
	
}
