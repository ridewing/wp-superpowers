<?php namespace SuperPowers\Datasource;

use SuperPowers\Core\SuperObject;

abstract class SuperDatasource extends SuperObject {

	protected $data = array();

	function isGrouped(){
		return false;
	}
	abstract function get($args = null);
}