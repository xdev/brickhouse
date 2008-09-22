<?php

/* OVERRIDE DEFAULT ROUTES ----------------------------------------------- */

// Override default homepage (index)
//$routes['default_home'] = array('uri' => "/^\/$/");

// Override default controller
//$routes['default_controller'] = array('uri' => "/^\/(?<controller>[a-z0-9_-]+)$/i");

// Override default controller+action
//$routes['default_controller_action'] = array('uri' => "/^\/(?<controller>[a-z0-9_-]+)\/(?<action>[a-z0-9_-]+)$/i");


/* USER DEFINED ROUTES --------------------------------------------------- */

// The $routes[] array doesn't need a specific key defined, unless you are overriding the 'default_*' routes

// Sample additional arguments to specific controller/action
//$routes[] = array('uri' => "/^\/(?<controller>blog)\/(?<action>archive)\/(?<id>[0-9]+)$/i");

// Sample controller override
/*
$routes[] = array(
	'uri' => "/^\/(blog)\/(?<action>archive)\/(?<id>[0-9]+)$/i",
	'controller' => 'actual_blog_controller'
);
*/