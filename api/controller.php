<?php

require "base.php";

if(!empty($_GET['controller']) && !empty($_GET['method'])){


	/*$superPowers->load->controllerFile('SuperTypeController');*/
	/** @var \SuperPowers\Controller\SuperTypeController $controller */
	$controller = $superPowers->load->controller($_GET['controller']);
	$controller->load($_GET['controller'], null);

	$args = null;

	if(!empty($_GET['args'])){
		$args = $_GET['args'];
	}

	$resp = call_user_func( array( $controller, $_GET['method'] ), $args );

	response($resp);
}
else {
	response(array('success' => false));
}


