<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Html extends SuperObject {

	function render($itemPath, $params = null){
		ob_start();

		if(!empty($params))
			extract($params);

		include $itemPath;

		$ret = ob_get_contents();
		ob_end_clean();

		return $ret;
	}

	function getCachedView($name, $postId) {
		if($this->config->get('settings.cache')) {

			if($content = $this->cache->getView($name, $postId)){
				return $content;

			}
			return false;
		}

		return false;
	}

	function getView($name, $params = null) {

		$viewName = str_replace('.', '/',  $name);

		$view = $this->load->view($viewName);

		if(empty($params))
		{
			$params = array();
		}

		/** @var \SuperPowers\Composer\SuperComposer $composer */
		$composer = $this->load->composer($name, $params);

		if($composer){
			$params = $composer->view($params);
		}

		// Add application to view
		$params['app'] = $this->app;

		$content = $this->render($view, $params);

		return $content;
	}

	function view($name, $params = null, $useCache = true) {

		$parts = array_map(function($part) {
			return ucfirst($part);
		}, explode('.', $name));

		$name = implode('.', $parts);

		$content = $this->getView($name, $params, $useCache);

		echo $content;
	}
}