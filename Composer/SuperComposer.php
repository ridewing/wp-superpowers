<?php namespace SuperPowers\Composer;

use SuperPowers\Core\SuperObject;

abstract class SuperComposer extends SuperObject {

	abstract function view($params = array());

	function load($params = array()){

	}

	public $cached = true;
	public $context = null;
}