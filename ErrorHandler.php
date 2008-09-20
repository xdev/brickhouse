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
		print '<div class="error" style="font-family: Georgia; background:#FFCCCC;">' . $message . '</div>';
	}
	
	public static function capture($n, $m, $f, $l)
	{
		$r = '<dl class="error" style="font-family: Georgia; background:#FFCCCC;">';
		$r .= '<dt>error</dt><dd>'.$n.'</dd>';
		$r .= '<dt>message</dt><dd>'.$m.'</dd>';
		$r .= '<dt>file</dt><dd>'.$f.'</dd>';
		$r .= '<dt>line</dt><dd>'.$l.'</dd>';
		$r .= '</dl>';
		print $r;
		return false;
		//mail('bug_central@1001journals.com', '1001 System Error', $message);
	}
	
}

