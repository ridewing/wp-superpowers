<?php namespace SuperPowers;

class Config extends SuperObject {

	private $path;

	function __construct()
	{
		parent::__construct();
		$this->path = SUPERPOWERS_APPLICATION_DIR . '/config';
	}

	function get($key)
	{
		$set = $this->_getFileAndKey($key);
		$cacheKey = $this->cache->getKey($set->file, 'config');

		if($this->cache->exists($cacheKey)){
			$content = $this->cache->get($cacheKey);
		}

		if(empty($content) && file_exists("{$this->path}/{$set->file}.php")) {
			$content = include "{$this->path}/{$set->file}.php";
			$this->cache->set($cacheKey, $content);
		}

		if(!empty($content)){

			if(empty($set->key)) {
				return $content;
			}

			if(array_key_exists($set->key, $content)) {

				return $content[$set->key];
			}
		}

		return null;
	}

	private function _getFileAndKey($key)
	{
		$parts = explode('.', $key);

		return (object)array(
			'file'  => $parts[0],
			'key'   => !empty($parts[1])?$parts[1]:null
		);
	}
}