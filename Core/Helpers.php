<?php

/**
 * @param string $name
 * @param array $params
 */
function _view($name, Array $params = array()) {

	global $superPowers;
	$superPowers->html->view($name, $params);
}

/**
 * @param string $name
 * @param array $params
 * @return string
 */
function _getView($name, Array $params = array()) {
	global $superPowers;
	return $superPowers->html->getView($name, $params);
}

/**
 * @return \SuperPowers\Controller\SuperTypeController
 */
function _controller() {
	global $superPowers;
	return $superPowers->controller;
}

/**
 * @param string $content
 * @param int $limit
 * @return string
 */
function _limit($content, $limit = 100) {
	global $superPowers;
	return $superPowers->post->limitContent($content, $limit);
}

function _url($path = "") {
	global $superPowers;
	echo $superPowers->url($path);
}

function _asset($file = "") {
	echo get_template_directory_uri() . $file;
}

function _s($string) {
	echo $string;
}

function _a($pathOrPostId, $content, $title = '', $classes = '') {


	if(is_int($pathOrPostId)){
		$url = get_permalink($pathOrPostId);
	}
	else {
		global $superPowers;
		$url = $superPowers->url($pathOrPostId);
	}

	if(empty($title)) {
		$title = $content;
	}


	echo "<a href='{$url}' class='{$classes}' title='{$title}'>{$content}</a>";
}

/**
 * @return string
 */
function _apiController(){
	global $superPowers;
	return "{$superPowers->url}/api/controller.php";
}

function _frontendControllers(){
	global $superPowers;
	echo "data-controller='{$superPowers->getFrontendControllers()}'";
}