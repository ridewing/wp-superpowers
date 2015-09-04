<?php namespace SuperPowers\Core;

class Cache {

	public static $cache = array();
	private $context = "";

	function exists($key) {
		$cache = self::$cache;

		return array_key_exists($key, $cache);
	}

	function get($key) {

		if($this->exists($key)) {
			return self::$cache[$key];
		}

		return null;
	}

	function set($key, $value) {
		self::$cache[$key] = $value;
	}

	function getKey($name, $type) {
		return strtolower("{$type}.{$name}");
	}

	function getView($name, $postId) {

		$path = $this->getViewPath($name, $postId);

		if(file_exists($path)){
			if(filemtime($path) < (60*60)){
				return file_get_contents($path);
			}

		}

		return false;
	}

	function setContext($context){
		$this->context = $context;
	}

	function getViewPath($view, $postId) {

		$view = trim($view, '/');
		$view = str_replace('/', '.', $view);

		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/views/{$postId}/{$this->context}/{$view}.view";

		return $path;
	}

	function storeView($name, $postId, $content) {
		$path = $this->getViewPath($name, $postId);

		$this->ensureStructure($path);
		file_put_contents($path, $content);
	}

	function removeView($name) {
		$path = $this->getViewPath($name);

		if(file_exists($path)){
			return unlink($path);
		}
	}

	function removeViewForPost($postId) {
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/views/{$postId}";

		if(file_exists($path)){
			return $this->deleteDir($path);
		}
	}

	function storePageMeta($url, $values){
		$key = md5($url);
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/meta/{$key}.meta";

		$this->ensureStructure($path);
		file_put_contents($path, json_encode($values));
	}

	function getPageMeta($url){
		$key = md5($url);
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/meta/{$key}.meta";

		if(file_exists($path)){
			if(filemtime($path) > (60*60)) {

			}

			return json_decode(file_get_contents($path), true);
		}

		return false;
	}

	function removePageMeta($url){
		$key = md5($url);
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/meta/{$key}.meta";

		if(file_exists($path)){
			return unlink($path);
		}
	}

	public function ensureStructure($path, $mode = 0775)
	{
		$dir = dirname($path);

		if (!file_exists($dir))
			return mkdir($dir, $mode, true);
		else
			return true;
	}


}