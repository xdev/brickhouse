<?php

class ErrorHandler
{	
	public $code;
	private static $messages;
	private static $instance = null;
	
	private function __construct()
	{
		$this->code = 200;
	}
	
	public static function getInstance()
	{
		if(!self::$instance){
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}	
	
	public static function message($message=null)
	{
		if ($message) self::$messages[] = $message;
		print $message;
	}
	
	public static function capture($n, $m, $f, $l)
	{
		$r = '';
		$r .= '<error>'.$n.'</error>';
		$r .= '<message>'.$m.'</message>';
		$r .= '<file>'.$f.'</file>';
		$r .= '<line>'.$l.'</line>';
		print $r;
		return true;
		//mail('bug_central@1001journals.com', '1001 System Error', $message);
	}
	
}

