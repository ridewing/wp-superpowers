<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

/**
 * Class Session
 * Class to store and fetch session data
 * @package SuperPowers
 */
class Session extends SuperObject {

	private $data = array();

	public function __construct()
	{

		parent::__construct();
		session_start();
		$this->data = $_SESSION;
	}


	function set($key, $value) {
		$this->data[$key] = $value;
		$_SESSION[$key] = $value;
	}

	function get($key, $default = null) {
		if(!array_key_exists($key, $this->data)) {
			return $default;
		}

		return $this->data[$key];
	}

	function remove($keys){

		if(!is_array($keys)) {
			$keys = array($keys);
		}

		foreach($keys as $key){
			if(array_key_exists($key, $this->data)){
				unset($_SESSION[$key]);
				unset($this->data[$key]);
			}

		}
	}

}