<?php namespace SuperPowers\Core;


class Config extends SuperObject {

	private $storage = [];

	function get($key, $default = null)
	{
		$set = $this->_getFileAndKey($key);

		if(isset($this->storage[$key])){
			$content = $this->storage[$key];
		}

		$path = SUPERPOWERS_APPLICATION_DIR . "/Config/{$set->file}.php";

		if(empty($content) && file_exists($path)) {
			$content = include $path;
			$this->storage[$key] = $content;
		}

		if(!empty($content)){

			if(empty($set->key)) {
				return $content;
			}

			if(array_key_exists($set->key, $content)) {

				return $content[$set->key];
			}
		}

		return $default;
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