<?php namespace SuperPowers\Core;

abstract class SuperRoute {

	static $routes = array();

	abstract function register();

	/**
	 * @param string $type
	 * @param string $subview
	 */
	function registerSubview($type, $subview) {
		$type = strtolower($type);
		$subview = strtolower($subview);

		// Will result in Ex. www.your-domain.com/game/12/videos
		$route = "{$type}/([^/]+)(/{$subview})/?$";
		$redirect = 'index.php?'.$type.'=$matches[1]&subview='.$subview;

		$this->add($route, $redirect);
		$this->addTag("subview");
	}

	/**
	 * @param string $path
	 * @param string $redirect
	 */
	function add($path, $redirect) {
		add_rewrite_rule($path, $redirect, 'top');
		static::$routes[$path] = $redirect;
	}

	/**
	 * @param string $tag
	 */
	function addTag($tag){
		add_rewrite_tag("%{$tag}%", '([^&]+)');
	}
}