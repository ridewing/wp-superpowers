<?php namespace SuperPowers\Model;

class SuperModel extends \SuperPowers\Core\SuperObject {

	/** @var \WP_Post  */
	private $object = null;
	private $_cache = [];

	function setObject($object) {
		$this->object = $object;
	}

	function __get($name)
	{
		$method = ucfirst($name);
		$method = "get{$method}";

		if(method_exists($this, $method)) {
			if(array_key_exists($name, $this->_cache)) {
				return $this->_cache[$name];
			}

			$value = call_user_func(array(&$this, $method));
			$this->_cache[$name] = $value;

			return $value;

		}

		if(property_exists($this->object, $name)) {
			return $this->object->{$name};
		}

		return parent::__get($name);
	}

}