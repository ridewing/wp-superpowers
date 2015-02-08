<?php namespace SuperPowers;

class ApplicationCache {

	public static $cache = array();

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

		$name = md5($name);
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/{$postId}/{$name}.php";

		if(file_exists($path)){
			return file_get_contents($path);
		}

		return false;
	}

	function storeView($name, $postId, $content) {

		$name = md5($name);
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/{$postId}/{$name}.php";

		$this->ensureStructure($path);
		file_put_contents($path, $content);
	}

	function removeView($name, $postId) {
		$name = md5($name);
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/{$postId}/{$name}.php";

		if(file_exists($path)){
			return unlink($path);
		}
	}

	function removeViewForPost($postId) {
		$path = SUPERPOWERS_APPLICATION_DIR . "/cache/{$postId}";

		if(file_exists($path)){
			return $this->deleteDir($path);
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

	public function deleteDir($dirPath) {
		if (! is_dir($dirPath)) {
			return false;
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
}