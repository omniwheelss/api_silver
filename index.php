<?php

	/**
		Gateway to redirect the page based on the input
	**/
	require_once 'controller/includes.php';

	$gateway = new GatewayController();
	
	$gateway->handleRequest();
?>
