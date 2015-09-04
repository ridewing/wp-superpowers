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