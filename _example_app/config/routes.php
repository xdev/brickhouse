<?php

// Default controller & action
$routes[] = array('uri' => "//");

// Specified controller & default action
$routes[] = array('uri' => "/(?<controller>[a-z0-9]+)/");

// Specified controller & action
$routes[] = array('uri' => "/(?<controller>[a-z0-9]+)\/(?<action>[a-z0-9]+)/");

/* USER DEFINED ROUTES --------------------------------------------------- */

// Sample additional arguments to specific controller/action
//$routes[] = array('uri' => "/(?<controller>blog)\/(?<action>archive)\/(?<id>[0-9]+)/");

// Sample controller override
/*
$routes[] = array(
	'uri' => "/(blog)\/(?<action>archive)\/(?<id>[0-9]+)/",
	'controller' => 'actual_blog_controller'
);
*/