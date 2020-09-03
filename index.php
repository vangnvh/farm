<?php

	require_once('config.php' );
	require_once('tool.php' );
	
	$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
	$uri = '/' . trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
	$uri = urldecode( $uri );
	include( ABSPATH .'contents/index.php' );
	exit();

?>