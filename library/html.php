<?php namespace SuperPowers;

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

	function getView($name, $params = null) {

		global $post;

		if($this->config->get('settings.cache')) {
			if($content = $this->cache->getView($name, $post->ID)){
				echo $content;
				return;
			}
		}

		$viewName = str_replace('.', '/',  $name);

		$view = $this->load->view($viewName);
		$composer = $this->load->composer($name);
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
			$this->cache->storeView($name, $post->ID, $content);
		}

		return $content;
	}

	function view($name, $params = null) {

		$content = $this->getView($name, $params);

		echo $content;
	}
}