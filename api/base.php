<?php

define("SUPERPOWERS_AJAX", true);

include(dirname(dirname(dirname(dirname(__FILE__)))) . '/wordpress/wp-load.php');

if(!function_exists('response')) {
	function response($resp){
		header('Content-Type: application/json');
		if(empty($resp)){
			$resp = array('success' => false, "error" => "No response returned from method");
		}
		echo json_encode($resp);
		exit;
	}
}