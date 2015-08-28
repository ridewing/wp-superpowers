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
		$this->viewcache->setComposerContext(null);

		$composer = $this->load->composer($name, $params);
		if($composer) {
			$this->viewcache->setComposerContext($composer->context);
		}

		if($this->config->get('settings.cache') && $this->app->controller->cached) {
			if($this->viewcache->exists($name)){
				echo $this->viewcache->get($name);
				exit();
			}
		}

		$viewName = str_replace('.', '/',  $name);

		$view = $this->load->view($viewName);

		if(empty($params))
		{
			$params = array();
		}

		if($composer){
			$params = $composer->view($params);
		}

		// Add application to view
		$params['app'] = $this->app;

		$content = $this->render($view, $params);

		if($this->config->get('settings.cache')) {
			if($composer){
				if( $composer->cached) {
					$this->viewcache->setComposerContext($composer->context);
					$this->viewcache->set($name,  $content);
				}
			}
			else {
				$this->viewcache->set($name,  $content);
			}

		}

		return $content;
	}

	function view($name, $params = null, $useCache = true) {

		$content = $this->getView($name, $params, $useCache);

		echo $content;
	}
}